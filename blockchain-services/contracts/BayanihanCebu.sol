// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract BayanihanCebu {
    // Contract owner (system wallet)
    address public owner;

    // Structs
    struct PhysicalDonation {
        uint256 id;
        string trackingCode;
        string barangayId;
        string donorName;
        string category;
        string quantity;
        uint256 estimatedValue;
        string ipfsHash; // Photos stored on IPFS
        uint256 timestamp;
    }

    struct OnlineDonation {
        uint256 id;
        string trackingCode;
        string barangayId;
        string donorName;
        uint256 amount;
        string paymentMethod;
        string txHash; // Original payment transaction
        uint256 timestamp;
    }

    struct Distribution {
        uint256 id;
        uint256 donationId;
        string donationType; // "physical" or "online"
        string distributedTo;
        string quantityDistributed;
        string ipfsHash; // Distribution photos
        uint256 timestamp;
    }

    struct ResourceNeed {
        uint256 id;
        string barangayId;
        string category;
        string description;
        string quantity;
        string urgency;
        uint256 timestamp;
    }

    // Storage
    mapping(uint256 => PhysicalDonation) public physicalDonations;
    mapping(uint256 => OnlineDonation) public onlineDonations;
    mapping(uint256 => Distribution) public distributions;
    mapping(uint256 => ResourceNeed) public resourceNeeds;

    uint256 public physicalDonationCount;
    uint256 public onlineDonationCount;
    uint256 public distributionCount;
    uint256 public resourceNeedCount;

    // Events
    event PhysicalDonationRecorded(uint256 indexed id, string trackingCode, string barangayId, uint256 timestamp);
    event OnlineDonationRecorded(uint256 indexed id, string trackingCode, string barangayId, uint256 amount, uint256 timestamp);
    event DistributionRecorded(uint256 indexed id, uint256 donationId, string donationType, uint256 timestamp);
    event ResourceNeedRecorded(uint256 indexed id, string barangayId, string urgency, uint256 timestamp);

    // Modifiers
    modifier onlyOwner() {
        require(msg.sender == owner, "Only owner can call this function");
        _;
    }

    constructor() {
        owner = msg.sender;
    }

    // Record Physical Donation
    function recordPhysicalDonation(
        uint256 _id,
        string memory _trackingCode,
        string memory _barangayId,
        string memory _donorName,
        string memory _category,
        string memory _quantity,
        uint256 _estimatedValue,
        string memory _ipfsHash
    ) public onlyOwner {
        physicalDonationCount++;

        physicalDonations[_id] = PhysicalDonation({
            id: _id,
            trackingCode: _trackingCode,
            barangayId: _barangayId,
            donorName: _donorName,
            category: _category,
            quantity: _quantity,
            estimatedValue: _estimatedValue,
            ipfsHash: _ipfsHash,
            timestamp: block.timestamp
        });

        emit PhysicalDonationRecorded(_id, _trackingCode, _barangayId, block.timestamp);
    }

    // Record Online Donation
    function recordOnlineDonation(
        uint256 _id,
        string memory _trackingCode,
        string memory _barangayId,
        string memory _donorName,
        uint256 _amount,
        string memory _paymentMethod,
        string memory _txHash
    ) public onlyOwner {
        onlineDonationCount++;

        onlineDonations[_id] = OnlineDonation({
            id: _id,
            trackingCode: _trackingCode,
            barangayId: _barangayId,
            donorName: _donorName,
            amount: _amount,
            paymentMethod: _paymentMethod,
            txHash: _txHash,
            timestamp: block.timestamp
        });

        emit OnlineDonationRecorded(_id, _trackingCode, _barangayId, _amount, block.timestamp);
    }

    // Record Distribution
    function recordDistribution(
        uint256 _id,
        uint256 _donationId,
        string memory _donationType,
        string memory _distributedTo,
        string memory _quantityDistributed,
        string memory _ipfsHash
    ) public onlyOwner {
        distributionCount++;

        distributions[_id] = Distribution({
            id: _id,
            donationId: _donationId,
            donationType: _donationType,
            distributedTo: _distributedTo,
            quantityDistributed: _quantityDistributed,
            ipfsHash: _ipfsHash,
            timestamp: block.timestamp
        });

        emit DistributionRecorded(_id, _donationId, _donationType, block.timestamp);
    }

    // Record Resource Need
    function recordResourceNeed(
        uint256 _id,
        string memory _barangayId,
        string memory _category,
        string memory _description,
        string memory _quantity,
        string memory _urgency
    ) public onlyOwner {
        resourceNeedCount++;

        resourceNeeds[_id] = ResourceNeed({
            id: _id,
            barangayId: _barangayId,
            category: _category,
            description: _description,
            quantity: _quantity,
            urgency: _urgency,
            timestamp: block.timestamp
        });

        emit ResourceNeedRecorded(_id, _barangayId, _urgency, block.timestamp);
    }

    // Get Physical Donation
    function getPhysicalDonation(uint256 _id) public view returns (PhysicalDonation memory) {
        return physicalDonations[_id];
    }

    // Get Online Donation
    function getOnlineDonation(uint256 _id) public view returns (OnlineDonation memory) {
        return onlineDonations[_id];
    }

    // Get Distribution
    function getDistribution(uint256 _id) public view returns (Distribution memory) {
        return distributions[_id];
    }

    // Get Resource Need
    function getResourceNeed(uint256 _id) public view returns (ResourceNeed memory) {
        return resourceNeeds[_id];
    }

    // Transfer ownership
    function transferOwnership(address newOwner) public onlyOwner {
        require(newOwner != address(0), "Invalid address");
        owner = newOwner;
    }
}
