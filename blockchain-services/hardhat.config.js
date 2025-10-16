require("@nomicfoundation/hardhat-toolbox");
require('dotenv').config();

module.exports = {
  solidity: "0.8.19",
  networks: {
    liskSepolia: {
      url: "https://rpc.sepolia-api.lisk.com",
      chainId: 4202,
      accounts: [process.env.SYSTEM_PRIVATE_KEY]
    }
  },
  etherscan: {
    apiKey: {
      liskSepolia: "your-etherscan-api-key" // Optional for verification
    }
  }
};
