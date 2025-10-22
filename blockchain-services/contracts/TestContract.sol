// SPDX-License-Identifier: MIT
pragma solidity ^0.8.30;

// Simple test contract to verify deployment works
contract TestContract {
    string public message;
    address public owner;

    constructor() {
        owner = msg.sender;
        message = "Hello from Lisk Sepolia!";
    }

    function setMessage(string memory newMessage) public {
        require(msg.sender == owner, "Not owner");
        message = newMessage;
    }

    function getMessage() public view returns (string memory) {
        return message;
    }
}
