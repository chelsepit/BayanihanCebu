# BayanihanCebu: Blockchain-Powered Disaster Relief Platform

<p align="center">
  <img src="https://via.placeholder.com/200" alt="BayanihanCebu Logo" width="200"/>
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"/></a>
  <a href="#"><img src="https://img.shields.io/badge/php-%3E%3D8.2-purple.svg" alt="PHP Version"/></a>
  <a href="#"><img src="https://img.shields.io/badge/laravel-12.x-red.svg" alt="Laravel"/></a>
  <a href="#"><img src="https://img.shields.io/badge/blockchain-Lisk-blue.svg" alt="Lisk"/></a>
</p>

<p align="center">
  <strong>Transparent, efficient, and blockchain-verified disaster relief coordination for Cebu communities</strong>
</p>

<p align="center">
  📺 <a href="#"><strong>Watch Demo Video</strong></a> | 
  🌐 <a href="#"><strong>Live Demo</strong></a> | 
  🔍 <a href="#"><strong>Blockchain Explorer</strong></a>
</p>

---

## 📑 Table of Contents

- [Overview](#-overview)
- [System Architecture](#-system-architecture)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [Getting Started](#-getting-started)
- [Blockchain Integration](#-blockchain-integration)
- [Security](#-security)
- [API Documentation](#-api-documentation)
- [Contributing](#-contributing)
- [Code of Conduct](#-code-of-conduct)
- [License](#-license)
- [Team](#-team)

---

## 🎯 Overview

### Problem Statement

During disasters in Cebu, relief operations face critical challenges:

- **Lack of transparency** in donation tracking leads to public distrust
- **Inefficient coordination** between barangays, BDRRMC, and LDRRMO
- **Delayed resource distribution** due to manual verification processes
- **Duplicate aid requests** waste limited resources
- **No accountability** in fund allocation and usage

### Our Solution

BayanihanCebu leverages **Lisk blockchain technology** to create a transparent, efficient, and accountable disaster relief ecosystem. By combining Laravel's robust backend with Lisk's blockchain infrastructure, we ensure **every donation is traceable**, **every resource request is verified**, and **every transaction is immutable**.

### Impact & Technology Integration

**Laravel Framework**: Powers our MVC architecture with:
- RESTful API design for seamless frontend-blockchain communication
- Eloquent ORM for optimized database queries and relationships
- Queue system for handling blockchain transactions asynchronously
- Event-driven architecture for real-time notifications

**Lisk Sepolia Blockchain**: Provides decentralized infrastructure for:
- Immutable donation records preventing fraud and manipulation
- Smart contract automation for fund distribution rules
- Transparent transaction history accessible to all stakeholders
- Trustless verification eliminating intermediary dependencies

**Web3.php Library**: Enables blockchain interaction through:
- JSON-RPC connection to Lisk Sepolia RPC endpoints
- Contract deployment and interaction
- Transaction signing with private keys
- Event listening for real-time blockchain confirmations

---

## 🏗 System Architecture

### High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                                  │
│  (Browser: Blade Templates + Tailwind CSS + Alpine.js)             │
└────────────────────────────────┬────────────────────────────────────┘
                                 │ HTTPS
┌────────────────────────────────┴────────────────────────────────────┐
│                   LARAVEL APPLICATION LAYER                          │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │                      WEB LAYER                               │   │
│  │  • Routes (web.php, api.php)                                │   │
│  │  • Middleware (Auth, CORS, Rate Limiting)                   │   │
│  │  • Controllers (REST API + Blade Views)                     │   │
│  └───────────────────────────┬─────────────────────────────────┘   │
│                              │                                       │
│  ┌───────────────────────────┴─────────────────────────────────┐   │
│  │                  BUSINESS LOGIC LAYER                        │   │
│  │  • Services (DisasterService, DonationService)              │   │
│  │  • Repositories (Database Abstraction)                      │   │
│  │  • Validators (Form Requests)                               │   │
│  │  • Jobs (Queue Workers for Blockchain Tx)                   │   │
│  └───────────────────────────┬─────────────────────────────────┘   │
│                              │                                       │
│  ┌───────────────────────────┴─────────────────────────────────┐   │
│  │                   DATA ACCESS LAYER                          │   │
│  │  • Eloquent Models (User, Disaster, Donation)               │   │
│  │  • Database Migrations & Seeders                            │   │
│  │  • donation_blockchain_records (Links off-chain to on-chain)│   │
│  └───────────────────────────┬─────────────────────────────────┘   │
└──────────────────────────────┼──────────────────────────────────────┘
                               │ MySQL │ HTTP/WebSocket
              ┌────────────────┴────────┴────────────────┐
              │                                           │
     ┌────────┴──────────┐                    ┌─────────┴──────────┐
     │  MYSQL DATABASE   │                    │  NODE.JS SERVICE   │
     │  ┌──────────────┐ │                    │  ┌───────────────┐ │
     │  │ • Users      │ │                    │  │ • Web3.js     │ │
     │  │ • Disasters  │ │                    │  │ • Provider    │ │
     │  │ • Donations  │ │                    │  │ • Contract    │ │
     │  │ • Resources  │ │                    │  │   ABIs        │ │
     │  │ • Barangays  │ │                    │  │ • Event       │ │
     │  │ • Off-chain  │ │                    │  │   Listeners   │ │
     │  │   Data       │ │                    │  └───────────────┘ │
     │  └──────────────┘ │                    └─────────┬──────────┘
     └───────────────────┘                              │ JSON-RPC
                                             ┌──────────┴──────────┐
                                             │ LISK SEPOLIA        │
                                             │    BLOCKCHAIN       │
                                             │  ┌────────────────┐ │
                                             │  │ Smart Contracts│ │
                                             │  │• Donation.sol  │ │
                                             │  │• Resource.sol  │ │
                                             │  │• Verification  │ │
                                             │  │  .sol          │ │
                                             │  └────────────────┘ │
                                             │  • Immutable       │
                                             │    Transaction     │
                                             │    Ledger          │
                                             │  • Decentralized   │
                                             │    State Storage   │
                                             └────────────────────┘
```

### Component Responsibilities

#### 1. Laravel Application Layer

**Responsibility**: Core business logic, API management, authentication, and authorization

**Key Components**:
- **Controllers**: Handle HTTP requests, delegate to services
  - `DisasterController`: CRUD operations for disaster events
  - `DonationController`: Process donations, trigger blockchain transactions
  - `ResourceController`: Manage resource requests and matching
  - `UserController`: User management and role assignments

- **Services**: Encapsulate business logic
  - `BlockchainService`: Interface with Node.js blockchain layer
  - `NotificationService`: Send email/SMS alerts
  - `GeocodingService`: Process location data

- **Models**: Eloquent ORM for database interactions
  - Relationships defined between Users, Disasters, Donations, Resources
 
- **Jobs & Queues**: Async processing
  - `ProcessBlockchainTransaction`: Queue blockchain writes
  - `SendDonationReceipt`: Generate and email receipts

**Technologies**: Laravel 12.x, PHP 8.2+, Blade, Laravel Sanctum (API tokens)

#### 2. Node.js Blockchain Service

**Responsibility**: Direct blockchain interaction, event monitoring, WebSocket real-time updates

**Key Components**:
- **Web3.js Provider**: Connects to Lisk Sepolia RPC endpoints
- **Contract Manager**: Deploys and interacts with smart contracts
- **Event Listener**: Monitors blockchain events (DonationReceived, ResourceAllocated)
- **WebSocket Server**: Pushes real-time updates to frontend (Socket.io)

**Technologies**: Node.js 18+, Web3.js/Ethers.js, Socket.io, Express.js

#### 3. MySQL Database

**Responsibility**: Off-chain data persistence, fast queries, user management

**Schema Structure**:
- `users`: User profiles, roles (Resident, BDRRMC, LDRRMO)
- `barangays`: Cebu barangay information, geographic boundaries
- `disasters`: Disaster events, severity, affected areas
- `donations`: Donation metadata (on-chain hash stored)
- `resources`: Resource inventory, requests, allocations
- `donation_blockchain_records`: Links off-chain records to blockchain transactions

**Technologies**: MySQL 8.0+, Eloquent ORM

#### 4. Lisk Sepolia Blockchain

**Responsibility**: Immutable transaction storage, smart contract execution, trustless verification

**Smart Contracts**:
- `DonationContract.sol`: Records donations with donor, amount, timestamp
- `ResourceContract.sol`: Tracks resource distribution, prevents double-allocation
- `VerificationContract.sol`: Multi-sig approval for large fund releases

**Technologies**: Solidity, Lisk Sepolia Testnet, Hardhat (deployment)

---

## ✨ Key Features

### 🔐 Secure Multi-Role Authentication System

**Role-Based Access Control (RBAC)**: Four distinct user roles

- **Resident**: Submit disaster reports, make donations, request resources
- **BDRRMC**: Verify barangay-level reports, allocate resources
- **LDRRMO**: City-wide disaster coordination, approve fund releases
  
**Barangay-Specific Permissions**: Users restricted to their geographic jurisdiction

**Laravel Sanctum**: API token authentication for mobile/external integrations

### 📡 Real-Time Donation Management

**Instant Reporting**: Residents submit geotagged disaster reports with photos

**Severity Tracking of the Resource Needs**: Automatic classification (Low, Medium, High, Critical)

**GIS Integration**: Interactive maps showing affected areas and their resource needs

**Status Updates**: For the barangays who's their resource need are: not attended, in-transit, received

### 💎 Blockchain-Verified Donations

**On-Chain Transparency**: Every donation recorded on Lisk Sepolia

**Immutable Audit Trail**: Transaction hashes prevent tampering

**Real-Time Tracking**: Donors see fund allocation in real-time

**Automated Receipts**: PDF generation with blockchain verification link

**Smart Contract Rules**: Pre-defined fund distribution logic 

**Multi-Currency Support**: PHP (fiat) and cryptocurrency donations

### 📊 Intelligent Resource Management

**Request Submission**: Barangays submit needs (food, water, medical supplies)

**Matching Algorithm**: Auto-match available resources to urgent requests

**Inventory System**: Real-time stock levels across all warehouses

**Distribution Tracking**: GPS-enabled delivery confirmation

**Duplicate Prevention**: Blockchain ensures no double-claiming of resources

### 📈 Analytics & Reporting

**Dashboard Visualizations**: Charts showing donation trends, resource usage

**Impact Metrics**: Families helped, resources distributed, response times

**Blockchain Explorer**: Custom UI to browse all on-chain transactions

**Export Functionality**: Generate compliance reports for government audits

---

## 🛠 Technology Stack

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.2+ | Server-side language |
| **Laravel** | 12.x | MVC framework, API development |
| **MySQL** | 8.0+ | Relational database |
| **Redis** | 7.0+ | Queue backend, caching |

### Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Blade** | (Laravel) | Server-side templating |
| **Tailwind CSS** | 3.x | Utility-first CSS framework |
| **Alpine.js** | 3.x | Lightweight JS reactivity |

### Blockchain

| Technology | Version | Purpose |
|------------|---------|---------|
| **Lisk Sepolia** | Testnet | Blockchain network |
| **Solidity** | 0.8.x | Smart contract language |
| **Web3.php** | 4.x | Blockchain interaction library |
| **Hardhat** | 2.x | Smart contract deployment |

### Testing & Code Quality

| Tool | Purpose |
|------|---------|
| **PHPUnit** | PHP unit & feature testing |
| **Pest** | Laravel testing framework |
| **GGShield** | Secret scanning, API key leak prevention |
| **Prettier** | Code formatting (JS/CSS) |
| **PHP CS Fixer** | PHP code style enforcement |
| **Larastan** | Static analysis for Laravel |

### DevOps

| Tool | Purpose |
|------|---------|
| **Docker** | Containerization |
| **GitHub Actions** | CI/CD pipeline |
| **Nginx** | Web server |
| **Supervisor** | Queue worker management |

---

## 🚀 Getting Started

### Prerequisites

Ensure you have the following installed:

```bash
# Check PHP version (requires 8.2+)
php -v

# Check Composer
composer -V

# Check Node.js (requires 18.0+)
node -v

# Check NPM
npm -v

# Check MySQL
mysql --version

# Check Git
git --version
```

**Required Software**:
- PHP >= 8.2 with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer >= 2.5
- Node.js >= 18.0 & NPM >= 9.0
- MySQL >= 8.0 or MariaDB >= 10.3
- Redis >= 7.0 (for queues)
- Git

### Installation

#### 1. Clone the Repository

```bash
git clone https://github.com/carlreyyy/BayanihanCebu.git
cd BayanihanCebu
```

#### 2. Install PHP Dependencies

```bash
composer install
```

This installs Laravel framework, PHPUnit, and all PHP packages defined in `composer.json`.

#### 3. Install Node.js Dependencies

```bash
npm install
```

This installs Tailwind CSS, Prettier, Alpine.js, and blockchain service dependencies.

#### 4. Setup Git Hooks (Husky)

```bash
npm run prepare
```

This configures pre-commit hooks for:
- GGShield secret scanning
- Prettier code formatting
- PHPUnit test execution

### Environment Configuration

#### 1. Create Environment File

```bash
cp .env.example .env
```

#### 2. Generate Application Key

```bash
php artisan key:generate
```

#### 3. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bayanihancebu
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 4. Configure Blockchain Settings
Blockchain Wallet Setup
Get Your Wallet Address & Private Key
Option A: Using MetaMask

Open MetaMask browser extension
Click on your account name at the top
Copy your wallet address (starts with 0x...)
To export private key:

Click three dots → Account Details → Show Private Key
Enter password
Copy the key (remove the 0x prefix for .env)



Option B: Create New Wallet with Web3
javascript// You can use Node.js to generate a new wallet
const { ethers } = require('ethers');
const wallet = ethers.Wallet.createRandom();
console.log('Address:', wallet.address);
console.log('Private Key:', wallet.privateKey);
Configure Wallet in .env
env# Your platform's main wallet (receives donations)
BAYANIHAN_CEBU_WALLET=0x1234567890abcdef1234567890abcdef12345678

# Admin wallet for contract interactions
BLOCKCHAIN_ADMIN_WALLET=#your blockchain admin wallet address (e.g., 0x...)

# Private key (64 characters, NO 0x prefix!)
ADMIN_WALLET_PRIVATE_KEY=#your admin wallet private key (64 characters, no 0x prefix)
BLOCKCHAIN_ADMIN_PRIVATE_KEY=#your blockchain admin private key (64 characters, no 0x prefix)

⚠️ SECURITY WARNING:
NEVER share your private key
NEVER commit .env to Git
Use test wallets for development
Keep production keys in secure environment variables

4. Get Test ETH for Lisk Sepolia

Visit Lisk Sepolia Faucet: https://sepolia-faucet.lisk.com/
Enter your wallet address
Request test ETH
Wait for confirmation (check: https://sepolia-blockscout.lisk.com)

5. Deploy Smart Contracts
If you haven't deployed contracts yet:

Navigate to your contracts directory:

bashcd blockchain/contracts

Install dependencies:

bashnpm install

Configure Hardhat (hardhat.config.js):

javascriptrequire("@nomicfoundation/hardhat-toolbox");
require('dotenv').config();

module.exports = {
  solidity: "0.8.19",
  networks: {
    liskSepolia: {
      url: process.env.LISK_RPC_URL,
      accounts: [process.env.ADMIN_WALLET_PRIVATE_KEY],
      chainId: 4202
    }
  }
};

Deploy contracts:

bash# Deploy DonationRecorder contract
npx hardhat run scripts/deploy-donation-recorder.js --network liskSepolia

# Deploy other contracts as needed
npx hardhat run scripts/deploy-resource.js --network liskSepolia
npx hardhat run scripts/deploy-verification.js --network liskSepolia

Copy contract addresses to .env:

envDONATION_RECORDER_CONTRACT_ADDRESS=0xYourDeployedContractAddress
DONATION_CONTRACT_ADDRESS=0xYourDeployedContractAddress
RESOURCE_CONTRACT_ADDRESS=0xYourDeployedContractAddress
VERIFICATION_CONTRACT_ADDRESS=0xYourDeployedContractAddress
6. Contract ABI Setup
After deployment, copy your contract ABI:
bash# Create contracts directory in storage
mkdir -p storage/contracts

# Copy ABI file
cp blockchain/contracts/artifacts/contracts/YourContract.sol/YourContract.json storage/contracts/contract_abi.json
Update .env:
envCONTRACT_ABI_PATH=./storage/contracts/contract_abi.json
7. PayMongo Configuration (For Philippine Payments)
You're already using test keys. For production:

Sign up at: https://dashboard.paymongo.com
Get your live keys from dashboard
Update .env:

PAYMONGO_PUBLIC_KEY=pk_live_your_live_public_key
PAYMONGO_SECRET_KEY=sk_live_your_live_secret_key
8. Optional: Node.js Service Setup
If you're using a separate Node.js service for blockchain operations:

Generate a secure secret:

bashphp artisan key:generate --show

Update .env:

NODE_SERVICE_URL=http://localhost:3000
NODE_SERVICE_SECRET=base64:YourGeneratedSecretKey
🧪 Testing Your Configuration
Test Blockchain Connection
bashphp artisan tinker
php// In Tinker
$web3 = new Web3\Web3(new Web3\Providers\HttpProvider(env('LISK_RPC_URL')));
$eth = $web3->eth;
$eth->blockNumber(function ($err, $blockNumber) {
    echo "Current block: " . $blockNumber . "\n";
});
Test Contract Interaction
bashphp artisan blockchain:test-connection
Verify Wallet Balance
Check your wallet at: https://sepolia-blockscout.lisk.com/address/YOUR_WALLET_ADDRESS
📋 Complete .env Example (DEVELOPMENT)
env# Laravel
APP_NAME=BayanihanCebu
APP_ENV=local
APP_KEY=base64:generated_key_here
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bayanihan_cebu
DB_USERNAME=root
DB_PASSWORD=

# Lisk Blockchain (Testnet)
LISK_NETWORK=lisk-sepolia
LISK_RPC_URL=https://rpc.sepolia-api.lisk.com
LISK_CHAIN_ID=4202
BLOCKCHAIN_ENABLED=true

# Wallets (Use test wallets!)
BAYANIHAN_CEBU_WALLET= #your BayanihanCebu platform wallet address (e.g., 0x...)
BLOCKCHAIN_ADMIN_WALLET=#your admin wallet private key (64 characters, no 0x prefix)
ADMIN_WALLET_PRIVATE_KEY= #your blockchain admin private key (64 characters, no 0x prefix)

# Smart Contracts (After deployment)
DONATION_RECORDER_CONTRACT_ADDRESS=0xYourContractAddress
DONATION_CONTRACT_ADDRESS=0xYourContractAddress

# PayMongo (Test keys)
PAYMONGO_PUBLIC_KEY= #your PayMongo public key
PAYMONGO_SECRET_KEY= #your PayMongo secret key
PAYMONGO_WEBHOOK_SECRET= #your PayMongo webhook secret
PAYMONGO_WEBHOOK_URL= #your PayMongo webhook URL

#### 5. Configure Queue & Cache

```env
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Running the Application

#### 1. Run Database Migrations

```bash
php artisan migrate
```

This creates all necessary tables: users, barangays, disasters, donations, resources, etc.

#### 2. Seed Database (Optional)

```bash
php artisan db:seed
```

Seeds the database with:
- Sample barangays from Cebu
- Test user accounts (one per role)
- Demo disaster events

#### 3. Compile Frontend Assets

```bash
# Development (with hot reload)
npm run dev

# Production (optimized)
npm run build
```

#### 4. Start Laravel Development Server

```bash
php artisan serve
```

Application runs at `http://localhost:8000`

#### 5. Start Queue Workers

In a separate terminal:

```bash
php artisan queue:work
```

This processes blockchain transactions and sends notifications.

#### 6. Start Node.js Blockchain Service

Navigate to blockchain service directory:

```bash
cd blockchain-service
npm install
npm start
```

Service runs at `http://localhost:3000`

#### 7. Access the Application

Open browser: `http://localhost:8000`

**Test Accounts** (if seeded):

- **LDRRMO**: ldrrmo@cebu.gov.ph / ldrrmo123
- **BDRRMC**: bdrrmc@lahug.cebu.gov.ph / bdrrmc123
- **Resident**: resident@example.com / resident123

---

## 🔗 Blockchain Integration

### Lisk Sepolia Configuration

**Network Details**:
- **Network Name**: Lisk Sepolia
- **RPC URL**: https://rpc.sepolia-api.lisk.com
- **Chain ID**: 4202
- **Currency Symbol**: ETH
- **Block Explorer**: https://sepolia-blockscout.lisk.com

### Smart Contracts

#### 1. DonationContract.sol

**Purpose**: Records all donations with cryptographic proof

**Key Functions**:

```solidity
function recordDonation(
    address donor,
    uint256 amount,
    string memory purpose,
    string memory disasterId
) external returns (uint256 donationId)
```

**Events**:

```solidity
event DonationRecorded(
    uint256 indexed donationId,
    address indexed donor,
    uint256 amount,
    uint256 timestamp
)
```

**Deployment Address**: `0x...` (see .env file)

#### 2. ResourceContract.sol

**Purpose**: Tracks resource allocation and prevents double-spending

**Key Functions**:

```solidity
function allocateResource(
    string memory resourceId,
    string memory barangayId,
    uint256 quantity,
    string memory resourceType
) external returns (uint256 allocationId)

function verifyAllocation(uint256 allocationId) external view returns (bool)
```

**Deployment Address**: `0x...` (see .env file)

#### 3. VerificationContract.sol

**Purpose**: Multi-signature approval for large transactions

**Key Functions**:

```solidity
function submitTransaction(
    address destination,
    uint256 value,
    bytes memory data
) external returns (uint256 transactionId)

function confirmTransaction(uint256 transactionId) external

function executeTransaction(uint256 transactionId) external
```

**Deployment Address**: `0x...` (see .env file)

### Interacting with Blockchain

#### From Laravel Application

**Example: Recording a donation on blockchain**

```php
use App\Services\BlockchainService;

$blockchainService = app(BlockchainService::class);

$txHash = $blockchainService->recordDonation([
    'donor_address' => $user->wallet_address,
    'amount' => $donation->amount,
    'purpose' => $donation->purpose,
    'disaster_id' => $disaster->id,
]);

// Store transaction hash in database
$donation->update(['blockchain_tx_hash' => $txHash]);
```

#### From Node.js Service

**Example: Listening for blockchain events**

```javascript
const DonationContract = require('./contracts/DonationContract.json');
const contract = new web3.eth.Contract(
    DonationContract.abi,
    process.env.DONATION_CONTRACT_ADDRESS
);

contract.events.DonationRecorded({}, (error, event) => {
    if (error) console.error(error);
    
    // Notify Laravel application via WebSocket
    io.emit('donation_confirmed', {
        donationId: event.returnValues.donationId,
        txHash: event.transactionHash,
        blockNumber: event.blockNumber
    });
});
```

### Gas Optimization

To minimize transaction costs on Lisk Sepolia:

1. **Batch Transactions**: Group multiple donations into single contract call
2. **Event Indexing**: Use indexed parameters for efficient filtering
3. **Data Storage**: Store only critical data on-chain, metadata off-chain
4. **Gas Price Strategy**: Use Lisk's predictable gas pricing

---

## 🔒 Security

### Security Measures Implemented

#### 1. Secret Scanning (GGShield)

**What it does**: Prevents accidental commits of API keys, private keys, passwords

**Configuration** (`.gitsecret.yml`):

```yaml
version: 2
secret-scan:
  paths-ignore:
    - "**/*.md"
    - "**/tests/**"
  match-policies:
    - name: BlockchainKeys
      scanners:
        - private-key
        - api-key
```

**How to use**:

```bash
# Manual scan
ggshield secret scan repo .

# Automatic pre-commit scan (via Husky)
# Runs on every git commit
```

#### 2. Input Validation

All user inputs validated using Laravel Form Requests:

```php
// Example: StoreDonationRequest.php
public function rules(): array
{
    return [
        'amount' => 'required|numeric|min:1|max:1000000',
        'purpose' => 'required|string|max:500',
        'disaster_id' => 'required|exists:disasters,id',
        'payment_method' => 'required|in:cash,gcash,crypto',
    ];
}
```

#### 3. SQL Injection Prevention

Laravel Eloquent ORM uses parameterized queries by default:

```php
// Safe from SQL injection
$donations = Donation::where('user_id', $userId)
    ->where('status', 'verified')
    ->get();
```

#### 4. XSS Protection

Blade templating auto-escapes output:

```blade
{{-- Automatically escaped --}}
<p>{{ $user->name }}</p>

{{-- Raw HTML (use with caution) --}}
<p>{!! $trustedContent !!}</p>
```

#### 5. CSRF Protection

All POST/PUT/DELETE requests require CSRF tokens:

```blade
<form method="POST" action="/donations">
    @csrf
    <!-- Form fields -->
</form>
```

#### 6. Rate Limiting

API endpoints protected against abuse:

```php
// routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/donations', [DonationController::class, 'store']);
});
```

#### 7. Private Key Management

**Never** store private keys in code or version control:

- Store in `.env` file (excluded via `.gitignore`)
- Use environment variables in production
- Consider hardware wallets for production deployments
- Implement multi-sig for critical operations

#### 8. Dependency Security

Automated vulnerability scanning:

```bash
# PHP dependencies
composer audit

# Node.js dependencies
npm audit

# Fix vulnerabilities
npm audit fix
```

### Security Best Practices

✅ **DO**:
- Use environment variables for sensitive data
- Enable GGShield pre-commit hooks
- Regularly update dependencies
- Implement role-based access control
- Use HTTPS in production
- Enable Laravel's built-in security features

❌ **DON'T**:
- Commit `.env` files
- Hardcode API keys or private keys
- Disable CSRF protection
- Trust user input without validation
- Use `php artisan serve` in production
- Expose error details in production

### Reporting Security Issues

If you discover a security vulnerability, please email: **security@bayanihancebu.com**

**DO NOT** create a public GitHub issue.

---

## 📚 API Documentation

### Base URL

```
Development: http://localhost:8000/api
Production: https://api.bayanihancebu.com
```

### Authentication

All API requests require authentication token:

```bash
# Login to get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Response
{
  "token": "1|abc123...",
  "user": {...}
}

# Use token in subsequent requests
curl -X GET http://localhost:8000/api/disasters \
  -H "Authorization: Bearer 1|abc123..."
```

### Endpoints

#### Disasters

**List Disasters**

```http
GET /api/disasters
```

**Query Parameters**:
- `status` (optional): safe, at-risk, affected, critical
- `barangay_id` (optional): Filter by barangay

**Response**:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Flooding in Barangay Lahug",
      "description": "Heavy rainfall causing severe flooding",
      "severity": "high",
      "latitude": 10.3235,
      "longitude": 123.9010,
      "barangay_id": 1,
      "images": ["base64_encoded_image"]
    }
  ]
}
```

**Create Disaster Report**

```http
POST /api/disasters
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body**:

```json
{
  "title": "Flooding in Barangay Lahug",
  "description": "Heavy rainfall causing severe flooding",
  "severity": "high",
  "latitude": 10.3235,
  "longitude": 123.9010,
  "barangay_id": 1,
  "images": ["base64_encoded_image"]
}
```

**Get Disaster Details**

```http
GET /api/disasters/{id}
```

#### Donations

**List Donations**

```http
GET /api/donations
```

**Create Donation**

```http
POST /api/donations
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body**:

```json
{
  "amount": 1000,
  "purpose": "Relief goods for flood victims",
  "disaster_id": 5,
  "payment_method": "gcash",
  "donor_wallet_address": "0x123..." 
}
```

**Get Donation Receipt**

```http
GET /api/donations/{id}/receipt
```

**Verify Donation on Blockchain**

```http
GET /api/donations/{id}/verify
```

**Response**:

```json
{
  "verified": true,
  "tx_hash": "0xabc...",
  "block_number": 12345,
  "explorer_url": "https://sepolia-blockscout.lisk.com/tx/0xabc..."
}
```

#### Resources

**List Resource Requests**

```http
GET /api/resources
```

**Create Resource Request**

```http
POST /api/resources
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body**:

```json
{
  "resource_type": "food",
  "quantity": 100,
  "unit": "packs",
  "urgency": "high",
  "disaster_id": 5,
  "barangay_id": 1,
  "notes": "Rice and canned goods needed urgently"
}
```

**Allocate Resource**

```http
POST /api/resources/{id}/allocate
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body**:

```json
{
  "allocated_quantity": 50,
  "source_warehouse": "Central Warehouse",
  "estimated_delivery": "2025-10-25 14:00:00"
}
```

#### Blockchain

**Get Transaction Status**

```http
GET /api/blockchain/transaction/{txHash}
```

**Get Donation on Blockchain**

```http
GET /api/blockchain/donation/{donationId}
```

### Response Format

**Success Response**:

```json
{
  "success": true,
  "data": {...},
  "message": "Operation successful"
}
```

**Error Response**:

```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

### Full API Documentation

Interactive API documentation available at: `http://localhost:8000/docs`

Generated using Scribe with example requests and responses.

---

## Running Tests

### Run All Tests

```bash
# PHPUnit tests
php artisan test

# Or with Pest
./vendor/bin/pest
```

### Run Specific Test Suites

```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# With coverage report
php artisan test --coverage
```

### Run Code Quality Checks

```bash
# PHP CS Fixer (code style)
./vendor/bin/php-cs-fixer fix --dry-run

# GGShield (secret scanning)
ggshield secret scan repo .

# Prettier (JS/CSS formatting)
npm run format:check
```

### Run All Quality Checks

```bash
# Included in Husky pre-commit hook
npm run pre-commit
```

---

## Updating Documentation

After making code changes, update relevant documentation:

### 1. Update README

Edit `README.md` to reflect new features, API changes, or configuration options.

### 2. Update API Documentation

We use Laravel API resources and Scribe for auto-generation:

```bash
# Generate API documentation
php artisan scribe:generate
```

Documentation is created at `public/docs/index.html`

### 3. Update Architecture Diagrams

If system architecture changes, update diagrams in `docs/architecture/`:
- Use tools like draw.io, Lucidchart, or Mermaid
- Export as SVG/PNG and commit to repository

### 4. Update Smart Contract Documentation

After deploying new contracts:

```bash
cd blockchain-service
npm run generate-docs
```

This creates documentation from Solidity NatSpec comments.

### 5. Commit Documentation

```bash
git add README.md docs/ public/docs/
git commit -m "docs: update API documentation and architecture diagrams"
```

---

## 🤝 Contributing

We welcome contributions from the community! Whether it's bug fixes, new features, documentation improvements, or translations, your help makes BayanihanCebu better.

### How to Contribute

#### 1. Fork the Repository

Click the "Fork" button at the top right of this repository.

#### 2. Clone Your Fork

```bash
git clone https://github.com/YOUR_USERNAME/BayanihanCebu.git
cd BayanihanCebu
```

#### 3. Create a Feature Branch

```bash
# Create and switch to new branch
git checkout -b feature/your-feature-name

# Examples:
# git checkout -b feature/add-sms-notifications
# git checkout -b fix/donation-validation-bug
# git checkout -b docs/improve-api-documentation
```

**Branch Naming Convention**:
- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation updates
- `refactor/` - Code refactoring
- `test/` - Adding tests
- `chore/` - Maintenance tasks

#### 4. Make Your Changes

- Write clean, readable code following PSR-12 standards
- Add tests for new functionality
- Update documentation if needed
- Ensure all tests pass locally

```bash
# Run tests
php artisan test

# Run code quality checks
npm run pre-commit
```

#### 5. Commit Your Changes

```bash
git add .
git commit -m "feat: add SMS notification service"
```

**Commit Message Format**:

```
<type>: <subject>

<body (optional)>

<footer (optional)>
```

**Types**:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

**Examples**:

```bash
feat: add blockchain transaction retry mechanism

fix: resolve donation amount validation error

docs: update installation instructions for Windows

test: add unit tests for ResourceService
```

#### 6. Push to Your Fork

```bash
git push origin feature/your-feature-name
```

#### 7. Open a Pull Request

1. Go to the original repository
2. Click "New Pull Request"
3. Select your fork and branch
4. Fill out the PR template (see below)
5. Submit the pull request

### Pull Request Template

When opening a PR, please include:

```markdown
## Description
Brief description of changes made

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Related Issue
Fixes #(issue number)

## Changes Made
- Change 1
- Change 2
- Change 3

## Testing
- [ ] I have tested this code locally
- [ ] All existing tests pass
- [ ] I have added tests for new functionality
- [ ] I have updated documentation

## Screenshots (if applicable)
Add screenshots for UI changes

## Checklist
- [ ] My code follows the project's style guidelines
- [ ] I have performed a self-review of my code
- [ ] I have commented my code where necessary
- [ ] My changes generate no new warnings
- [ ] I have updated the README/documentation
- [ ] No secrets or API keys are committed
```

### Issue Templates

When reporting bugs or requesting features, please use our issue templates:

#### Bug Report Template

```markdown
**Describe the Bug**
A clear description of the bug

**To Reproduce**
Steps to reproduce:
1. Go to '...'
2. Click on '...'
3. See error

**Expected Behavior**
What you expected to happen

**Screenshots**
If applicable, add screenshots

**Environment**
- OS: [e.g., Windows 11, Ubuntu 22.04]
- PHP Version: [e.g., 8.2.12]
- Laravel Version: [e.g., 11.0]
- Browser: [e.g., Chrome 120]

**Additional Context**
Any other relevant information
```

#### Feature Request Template

```markdown
**Feature Description**
Clear description of the proposed feature

**Problem It Solves**
What problem does this feature address?

**Proposed Solution**
Your suggested implementation

**Alternatives Considered**
Other solutions you've considered

**Additional Context**
Mockups, examples, or references
```

### Development Guidelines

#### Code Style

**PHP**:
- Follow PSR-12 coding standard
- Use type hints for parameters and return types
- Write descriptive variable and function names
- Maximum line length: 120 characters

```php
// Good
public function calculateTotalDonations(int $disasterId): float
{
    return Donation::where('disaster_id', $disasterId)
        ->where('status', 'verified')
        ->sum('amount');
}

// Bad
public function calc($id) {
    return Donation::where('disaster_id',$id)->where('status','verified')->sum('amount');
}
```

**JavaScript**:
- Use ES6+ syntax
- Follow Prettier configuration
- Use camelCase for variables and functions

#### Testing Guidelines

**Write Tests For**:
- All new features
- Bug fixes (add regression test)
- Public API methods
- Critical business logic

**Test Structure**:

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Donation;

class DonationTest extends TestCase
{
    /** @test */
    public function user_can_create_donation()
    {
        // Arrange
        $user = User::factory()->create();
        $donationData = [
            'amount' => 1000,
            'purpose' => 'Test donation',
        ];
        
        // Act
        $response = $this->actingAs($user)
            ->post('/api/donations', $donationData);
        
        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('donations', [
            'user_id' => $user->id,
            'amount' => 1000,
        ]);
    }
}
```

#### Documentation Standards

- Update README for significant changes
- Add inline comments for complex logic
- Document public API methods with PHPDoc
- Update architecture diagrams if needed

```php
/**
 * Record a donation on the blockchain
 *
 * @param  array  $donationData  Contains donor_address, amount, purpose, disaster_id
 * @return string  Transaction hash
 * @throws \Exception  If blockchain transaction fails
 */
public function recordDonation(array $donationData): string
{
    // Implementation
}
```

### Code Review Process

All contributions go through code review:

1. **Automated Checks**: CI/CD runs tests, linters, security scans
2. **Peer Review**: At least one maintainer reviews code
3. **Feedback**: Reviewers may request changes
4. **Approval**: Once approved, PR is merged

**Review Criteria**:
- Code quality and readability
- Test coverage
- Documentation completeness
- Security considerations
- Performance implications

### Getting Help

**Questions**: Open a discussion in [GitHub Discussions](https://github.com/chelsepit/BayanihanCebu/discussions)
**Bugs**: Create an issue with bug report template
**Features**: Open an issue with feature request template
**Chat**: Join our [Discord server](#) [invite link]

### Recognition

Contributors will be:
- Listed in CONTRIBUTORS.md
- Mentioned in release notes
- Featured on project website (with permission)

---

## 📜 Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all. We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

**Positive Behavior**:
- Using welcoming and inclusive language
- Being respectful of differing viewpoints
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards others

**Unacceptable Behavior**:
- Trolling, insulting/derogatory comments, and personal attacks
- Public or private harassment
- Publishing others' private information without permission
- Other conduct which could reasonably be considered inappropriate

### Enforcement

Project maintainers are responsible for clarifying standards and will take appropriate and fair corrective action in response to any behavior that violates this Code of Conduct.

**Reporting**: Report violations to conduct@bayanihancebu.com

All complaints will be reviewed and investigated promptly and fairly.

---

## 📄 License

This project is licensed under the **MIT License**.

### MIT License

```
MIT License

Copyright (c) 2025 BayanihanCebu Team

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

### Third-Party Licenses

This project uses open-source libraries with their respective licenses:

- **Laravel** - MIT License
- **Node.js** - MIT License
- **Tailwind CSS** - MIT License
- **Web3.php** - LGPL-3.0 License
- **MySQL** - GPL License (for MySQL Server)

See [LICENSE-THIRD-PARTY.md](LICENSE-THIRD-PARTY.md) for complete third-party license information.

---

## 👥 Team

### Core Contributors

| Name | Role | GitHub | Contact |
|------|------|--------|---------|
| **Chelsie Faith Maranga** | Team Lead | [@chelsepit](https://github.com/chelsepit) | cf.maranga@gmail.com |
| **Carl Rey Tibom** | Blockchain Developer | [@carlreyyy] | carlreyt@gmail.com |
| **[Jan Louise Baroro** | Frontend Developer UI/UX | [@Jlb-dot] | barorojan08@gmail.com |
| **[Judd Jala]** | Backend Developer | [@jadjala] | juddjala95@gmail.com |

### Contributors

We appreciate all contributors! See [CONTRIBUTORS.md](CONTRIBUTORS.md) for the full list.

### Acknowledgments

Special thanks to:

- **Cebu Hacktoberfest 2025** organizers for this opportunity
- **Lisk Foundation** for blockchain infrastructure
- **Local government units** in Cebu for domain expertise
- **Open source community** for tools and libraries

---

## 📞 Contact & Support

### General Inquiries

- **Email**: info@bayanihancebu.com
- **Website**: https://bayanihancebu.com

### Technical Support

- **GitHub Issues**: [Create an issue](https://github.com/carlreyyy/BayanihanCebu/issues)
- **Discord**: [Join our server](#) [invite link]
- **Documentation**: https://docs.bayanihancebu.com

### Social Media

- **Twitter**: [@BayanihanCebu](https://twitter.com/BayanihanCebu)
- **Facebook**: [BayanihanCebu](https://facebook.com/BayanihanCebu)

### Security

- **Report vulnerabilities**: security@bayanihancebu.com
- **PGP Key**: [Download](https://bayanihancebu.com/pgp-key)

---

## 🗺 Roadmap

### Phase 1: Foundation (Current)

- ✅ Core authentication system
- ✅ Resource reporting module
- ✅ Basic donation tracking
- ✅ Lisk Sepolia integration
- ⏳ Resource management system

### Phase 2: Enhancement (Q1 2026)

- ⬜ Mobile application (React Native)
- ⬜ SMS notification system
- ⬜ Advanced analytics dashboard
- ⬜ Multi-language support (Cebuano, Tagalog, English)
- ⬜ Integration with government APIs

### Phase 3: Scaling (Q2 2026)

- ⬜ Mainnet deployment on Lisk L1
- ⬜ AI-powered resource allocation
- ⬜ IoT sensor integration for real-time monitoring
- ⬜ Nationwide expansion beyond Cebu

### Phase 4: Innovation (Q3 2026)

- ⬜ NFT-based donation certificates
- ⬜ DAO governance for fund allocation
- ⬜ Cross-chain bridge for multi-blockchain support
- ⬜ Machine learning for disaster prediction

---

<p align="center">
  <strong>Built with ❤️ for Cebu | Powered by Blockchain | Driven by Community</strong>
</p>

<p align="center">
  <a href="#bayanihancebu-blockchain-powered-disaster-relief-platform">Back to Top ⬆️</a>
</p>
