const hre = require("hardhat");

async function main() {
    console.log("Deploying BayanihanCebu contract to Lisk Sepolia...");

    const BayanihanCebu = await hre.ethers.getContractFactory("BayanihanCebu");
    const contract = await BayanihanCebu.deploy();

    await contract.deployed();

    console.log("✅ BayanihanCebu deployed to:", contract.address);
    console.log("📝 Save this address to your .env file!");
    console.log("CONTRACT_ADDRESS=" + contract.address);
}

main()
    .then(() => process.exit(0))
    .catch((error) => {
        console.error(error);
        process.exit(1);
    });
