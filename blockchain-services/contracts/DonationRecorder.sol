// SPDX-License-Identifier: MIT
pragma solidity ^0.8.30;

contract DonationRecorder {
    address public owner;
    bool public paused = false;

    mapping(address => bool) public admins;
    mapping(string => DonationProof) private donations;

    enum DonationType { Money, Goods }

    struct DonationProof {
        string trackingCode;
        DonationType donationType;
        string offChainHash;
        uint256 timestamp;
        address donor;
        uint256 amount;
        string barangay;
    }

    event DonationRecorded(
        string trackingCode,
        uint256 amount,
        string barangay,
        DonationType donationType,
        address donor,
        uint256 timestamp,
        string offChainHash
    );

    event OwnershipTransferred(address indexed previousOwner, address indexed newOwner);
    event AdminAdded(address indexed admin);
    event AdminRemoved(address indexed admin);
    event PauseToggled(bool isPaused);

    constructor() {
        owner = msg.sender;
    }

    modifier onlyOwner() {
        require(msg.sender == owner, "Not authorized: owner only");
        _;
    }

    modifier onlyAdmin() {
        require(admins[msg.sender] || msg.sender == owner, "Not authorized: admin only");
        _;
    }

    modifier whenNotPaused() {
        require(!paused, "Contract is paused");
        _;
    }

    function recordDonation(
        string memory trackingCode,
        DonationType donationType,
        string memory offChainHash,
        uint256 amount,
        string memory barangay
    ) public onlyAdmin whenNotPaused {
        require(bytes(trackingCode).length > 0, "Tracking code required");
        require(bytes(barangay).length > 0, "Barangay required");
        require(amount > 0, "Amount must be greater than 0");
        require(donations[trackingCode].timestamp == 0, "Donation already recorded");

        donations[trackingCode] = DonationProof({
            trackingCode: trackingCode,
            donationType: donationType,
            offChainHash: offChainHash,
            timestamp: block.timestamp,
            donor: msg.sender,
            amount: amount,
            barangay: barangay
        });

        emit DonationRecorded(
            trackingCode,
            amount,
            barangay,
            donationType,
            msg.sender,
            block.timestamp,
            offChainHash
        );
    }

    function getDonation(string memory trackingCode) public view returns (DonationProof memory) {
        require(donations[trackingCode].timestamp != 0, "Donation not found");
        return donations[trackingCode];
    }

    function transferOwnership(address newOwner) public onlyOwner {
        require(newOwner != address(0), "Invalid address");
        address oldOwner = owner;
        owner = newOwner;
        emit OwnershipTransferred(oldOwner, newOwner);
    }

    function addAdmin(address admin) public onlyOwner {
        require(admin != address(0), "Invalid address");
        require(!admins[admin], "Already an admin");
        admins[admin] = true;
        emit AdminAdded(admin);
    }

    function removeAdmin(address admin) public onlyOwner {
        require(admins[admin], "Not an admin");
        admins[admin] = false;
        emit AdminRemoved(admin);
    }

    function togglePause() public onlyOwner {
        paused = !paused;
        emit PauseToggled(paused);
    }
}
