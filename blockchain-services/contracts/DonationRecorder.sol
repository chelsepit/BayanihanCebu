// SPDX-License-Identifier: MIT
pragma solidity ^0.8.30;

contract DonationRecorder {
    address public owner;
    bool public paused = false;

    mapping(address => bool) public admins;

    enum DonationType { Money, Goods }

    event DonationRecorded(
        string trackingCode,
        uint256 amount,
        string barangayId,
        DonationType donationType,
        uint256 timestamp
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
        uint256 amount,
        string memory barangayId,
        DonationType donationType
    ) public onlyAdmin whenNotPaused {
        require(bytes(trackingCode).length > 0, "Tracking code required");
        require(bytes(barangayId).length > 0, "Barangay ID required");
        require(amount > 0, "Amount must be greater than 0");

        emit DonationRecorded(
            trackingCode,
            amount,
            barangayId,
            donationType,
            block.timestamp
        );
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
