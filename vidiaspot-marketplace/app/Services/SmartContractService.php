<?php

namespace App\Services;

use App\Models\SmartContract;
use App\Models\SmartContractTransaction;
use App\Models\Ad;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Auth;

class SmartContractService
{
    protected $blockchainService;

    public function __construct(BlockchainService $blockchainService)
    {
        $this->blockchainService = $blockchainService;
    }

    public function deployContract($contractData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        // Validate contract data
        if (!isset($contractData['abi']) || !isset($contractData['bytecode'])) {
            throw new \Exception('ABI and bytecode are required for contract deployment');
        }

        // Deploy the contract to the blockchain
        $deploymentResult = $this->blockchainService->deployContract(
            $contractData['bytecode'],
            $contractData['abi'],
            $contractData['constructor_args'] ?? []
        );

        if (!$deploymentResult['success']) {
            throw new \Exception('Contract deployment failed: ' . $deploymentResult['error']);
        }

        // Create a record of the smart contract in our database
        $smartContract = SmartContract::create([
            'name' => $contractData['name'],
            'description' => $contractData['description'] ?? '',
            'contract_address' => $deploymentResult['contract_address'],
            'blockchain' => $contractData['blockchain'] ?? 'ethereum',
            'abi' => $contractData['abi'],
            'bytecode' => $contractData['bytecode'],
            'contract_type' => $contractData['type'] ?? 'standard',
            'status' => 'deployed',
            'creator_id' => $userId,
            'owner_id' => $userId,
            'version' => $contractData['version'] ?? '1.0.0',
            'gas_limit' => $contractData['gas_limit'] ?? 500000,
            'gas_price' => $contractData['gas_price'] ?? 20.00,
            'is_active' => true,
            'deployed_at' => now(),
            'parameters' => $contractData['parameters'] ?? [],
            'functions' => $this->extractFunctionsFromABI($contractData['abi']),
            'events' => $this->extractEventsFromABI($contractData['abi']),
            'metadata' => $contractData['metadata'] ?? [],
        ]);

        return $smartContract;
    }

    public function executeContractFunction($contractId, $functionName, $parameters = [], $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $contract = SmartContract::find($contractId);
        if (!$contract) {
            throw new \Exception('Contract not found');
        }

        // Prepare the transaction
        $transactionData = [
            'contract_address' => $contract->contract_address,
            'function_name' => $functionName,
            'abi' => $contract->abi,
            'parameters' => $parameters,
            'from' => $this->getUserBlockchainAddress($userId),
            'gas_limit' => $contract->gas_limit,
            'gas_price' => $contract->gas_price,
        ];

        // Execute the transaction on the blockchain
        $result = $this->blockchainService->executeFunction($transactionData);

        // Record the transaction in our database
        $transaction = SmartContractTransaction::create([
            'smart_contract_id' => $contractId,
            'user_id' => $userId,
            'transaction_type' => 'function_call',
            'function_name' => $functionName,
            'parameters' => $parameters,
            'transaction_hash' => $result['transaction_hash'] ?? null,
            'blockchain' => $contract->blockchain,
            'from_address' => $transactionData['from'],
            'to_address' => $contract->contract_address,
            'value' => $transactionData['value'] ?? 0,
            'gas_used' => $result['gas_used'] ?? $contract->gas_limit,
            'gas_price' => $contract->gas_price,
            'status' => $result['status'] ?? 'pending',
            'error_message' => $result['error'] ?? null,
            'metadata' => [
                'function_params' => $parameters,
                'execution_data' => $result,
            ],
        ]);

        return [
            'transaction' => $transaction,
            'result' => $result,
        ];
    }

    public function createMarketplaceTransaction($adId, $buyerId = null, $amount, $currency = 'ETH', $contractId = null, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        $buyerId = $buyerId ?: $userId;
        
        $ad = Ad::find($adId);
        if (!$ad) {
            throw new \Exception('Ad not found');
        }

        // If no specific contract is provided, use the default marketplace contract
        if (!$contractId) {
            $contract = $this->getDefaultMarketplaceContract();
        } else {
            $contract = SmartContract::find($contractId);
        }

        if (!$contract) {
            throw new \Exception('Marketplace contract not found');
        }

        // Prepare marketplace transaction data
        $transactionData = [
            'contract_address' => $contract->contract_address,
            'function_name' => 'purchaseItem',
            'abi' => $contract->abi,
            'parameters' => [
                $ad->id,  // itemId
                $amount,  // amount
                $ad->user_id,  // sellerId
                $buyerId,  // buyerId
            ],
            'from' => $this->getUserBlockchainAddress($buyerId),
            'value' => $amount,
            'gas_limit' => $contract->gas_limit,
            'gas_price' => $contract->gas_price,
        ];

        // Execute the transaction on the blockchain
        $result = $this->blockchainService->executeFunction($transactionData);

        // Record the transaction in our database
        $transaction = SmartContractTransaction::create([
            'smart_contract_id' => $contract->id,
            'user_id' => $buyerId,
            'ad_id' => $adId,
            'transaction_type' => 'marketplace_purchase',
            'function_name' => 'purchaseItem',
            'parameters' => $transactionData['parameters'],
            'transaction_hash' => $result['transaction_hash'] ?? null,
            'blockchain' => $contract->blockchain,
            'from_address' => $transactionData['from'],
            'to_address' => $contract->contract_address,
            'value' => $amount,
            'gas_used' => $result['gas_used'] ?? $contract->gas_limit,
            'gas_price' => $contract->gas_price,
            'status' => $result['status'] ?? 'pending',
            'error_message' => $result['error'] ?? null,
            'metadata' => [
                'ad_details' => $ad->toArray(),
                'buyer_id' => $buyerId,
                'seller_id' => $ad->user_id,
                'amount' => $amount,
                'currency' => $currency,
            ],
        ]);

        return [
            'transaction' => $transaction,
            'result' => $result,
        ];
    }

    public function createEscrowTransaction($adId, $buyerId, $sellerId, $amount, $contractId = null, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $ad = Ad::find($adId);
        if (!$ad) {
            throw new \Exception('Ad not found');
        }

        // If no specific contract is provided, use the default escrow contract
        if (!$contractId) {
            $contract = $this->getDefaultEscrowContract();
        } else {
            $contract = SmartContract::find($contractId);
        }

        if (!$contract) {
            throw new \Exception('Escrow contract not found');
        }

        // Prepare escrow transaction data
        $transactionData = [
            'contract_address' => $contract->contract_address,
            'function_name' => 'createEscrow',
            'abi' => $contract->abi,
            'parameters' => [
                $adId,
                $buyerId,
                $sellerId,
                $amount,
            ],
            'from' => $this->getUserBlockchainAddress($buyerId),
            'value' => $amount,
            'gas_limit' => $contract->gas_limit,
            'gas_price' => $contract->gas_price,
        ];

        // Execute the transaction on the blockchain
        $result = $this->blockchainService->executeFunction($transactionData);

        // Record the transaction in our database
        $transaction = SmartContractTransaction::create([
            'smart_contract_id' => $contract->id,
            'user_id' => $buyerId,
            'ad_id' => $adId,
            'transaction_type' => 'escrow_creation',
            'function_name' => 'createEscrow',
            'parameters' => $transactionData['parameters'],
            'transaction_hash' => $result['transaction_hash'] ?? null,
            'blockchain' => $contract->blockchain,
            'from_address' => $transactionData['from'],
            'to_address' => $contract->contract_address,
            'value' => $amount,
            'gas_used' => $result['gas_used'] ?? $contract->gas_limit,
            'gas_price' => $contract->gas_price,
            'status' => $result['status'] ?? 'pending',
            'error_message' => $result['error'] ?? null,
            'metadata' => [
                'ad_details' => $ad->toArray(),
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
                'amount' => $amount,
                'escrow_terms' => $contractData['terms'] ?? [],
            ],
        ]);

        return [
            'transaction' => $transaction,
            'result' => $result,
        ];
    }

    public function releaseEscrow($transactionId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $originalTransaction = SmartContractTransaction::find($transactionId);
        if (!$originalTransaction || $originalTransaction->transaction_type !== 'escrow_creation') {
            throw new \Exception('Invalid escrow transaction');
        }

        $contract = SmartContract::find($originalTransaction->smart_contract_id);
        if (!$contract) {
            throw new \Exception('Contract not found');
        }

        // Prepare release transaction data
        $transactionData = [
            'contract_address' => $contract->contract_address,
            'function_name' => 'releaseEscrow',
            'abi' => $contract->abi,
            'parameters' => [
                $originalTransaction->id,  // escrowId
                $originalTransaction->ad_id,
                $originalTransaction->metadata['seller_id'],
            ],
            'from' => $this->getUserBlockchainAddress($userId),
            'gas_limit' => $contract->gas_limit,
            'gas_price' => $contract->gas_price,
        ];

        // Execute the transaction on the blockchain
        $result = $this->blockchainService->executeFunction($transactionData);

        // Record the release transaction in our database
        $releaseTransaction = SmartContractTransaction::create([
            'smart_contract_id' => $contract->id,
            'user_id' => $userId,
            'ad_id' => $originalTransaction->ad_id,
            'transaction_type' => 'escrow_release',
            'function_name' => 'releaseEscrow',
            'parameters' => $transactionData['parameters'],
            'transaction_hash' => $result['transaction_hash'] ?? null,
            'blockchain' => $contract->blockchain,
            'from_address' => $transactionData['from'],
            'to_address' => $contract->contract_address,
            'value' => $originalTransaction->value,
            'gas_used' => $result['gas_used'] ?? $contract->gas_limit,
            'gas_price' => $contract->gas_price,
            'status' => $result['status'] ?? 'pending',
            'error_message' => $result['error'] ?? null,
            'metadata' => [
                'original_transaction_id' => $originalTransaction->id,
                'released_by' => $userId,
            ],
        ]);

        return [
            'transaction' => $releaseTransaction,
            'result' => $result,
        ];
    }

    public function getContractTransactions($contractId, $filters = [])
    {
        $query = SmartContractTransaction::where('smart_contract_id', $contractId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function getContractById($contractId)
    {
        return SmartContract::with(['creator', 'owner'])->find($contractId);
    }

    public function getActiveContracts($userId = null, $filters = [])
    {
        $userId = $userId ?: Auth::id();
        
        $query = SmartContract::active();

        if (isset($filters['blockchain'])) {
            $query->byBlockchain($filters['blockchain']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['owned'])) {
            $query->where('owner_id', $userId);
        }

        return $query->with(['creator', 'owner'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransactionById($transactionId)
    {
        return SmartContractTransaction::with(['smartContract', 'user', 'ad'])->find($transactionId);
    }

    public function getUserBlockchainAddress($userId)
    {
        // In a real implementation, this would retrieve the user's blockchain address
        // from their profile or a blockchain wallet service
        // For now, we'll return a placeholder
        return '0x' . str_pad($userId, 40, '0', STR_PAD_LEFT);
    }

    public function getRecentTransactions($userId = null, $limit = 10)
    {
        $userId = $userId ?: Auth::id();
        
        return SmartContractTransaction::where('user_id', $userId)
            ->with(['smartContract', 'ad'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getBlockchainTransactionStatus($transactionHash, $blockchain = 'ethereum')
    {
        return $this->blockchainService->getTransactionStatus($transactionHash, $blockchain);
    }

    private function extractFunctionsFromABI($abi)
    {
        $functions = [];
        foreach ($abi as $item) {
            if (isset($item['type']) && $item['type'] === 'function') {
                $functions[] = [
                    'name' => $item['name'],
                    'inputs' => $item['inputs'] ?? [],
                    'outputs' => $item['outputs'] ?? [],
                    'stateMutability' => $item['stateMutability'] ?? '',
                ];
            }
        }
        return $functions;
    }

    private function extractEventsFromABI($abi)
    {
        $events = [];
        foreach ($abi as $item) {
            if (isset($item['type']) && $item['type'] === 'event') {
                $events[] = [
                    'name' => $item['name'],
                    'inputs' => $item['inputs'] ?? [],
                ];
            }
        }
        return $events;
    }

    private function getDefaultMarketplaceContract()
    {
        // Return a default marketplace contract, creating it if it doesn't exist
        $contract = SmartContract::where('name', 'Default Marketplace Contract')
            ->where('contract_type', 'marketplace')
            ->first();

        if (!$contract) {
            // Deploy a default marketplace contract
            $contract = $this->deployContract([
                'name' => 'Default Marketplace Contract',
                'description' => 'Default marketplace contract for secure transactions',
                'abi' => config('blockchain.default_marketplace_abi', []),
                'bytecode' => config('blockchain.default_marketplace_bytecode', ''),
                'type' => 'marketplace',
                'blockchain' => 'ethereum',
                'version' => '1.0.0',
                'gas_limit' => 500000,
                'gas_price' => 20.00,
            ], 1); // Use admin user ID
        }

        return $contract;
    }

    private function getDefaultEscrowContract()
    {
        // Return a default escrow contract, creating it if it doesn't exist
        $contract = SmartContract::where('name', 'Default Escrow Contract')
            ->where('contract_type', 'escrow')
            ->first();

        if (!$contract) {
            // Deploy a default escrow contract
            $contract = $this->deployContract([
                'name' => 'Default Escrow Contract',
                'description' => 'Default escrow contract for secure payments',
                'abi' => config('blockchain.default_escrow_abi', []),
                'bytecode' => config('blockchain.default_escrow_bytecode', ''),
                'type' => 'escrow',
                'blockchain' => 'ethereum',
                'version' => '1.0.0',
                'gas_limit' => 600000,
                'gas_price' => 20.00,
            ], 1); // Use admin user ID
        }

        return $contract;
    }
}