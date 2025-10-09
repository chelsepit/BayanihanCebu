🛡️ BAYANIHANCEBU — Barangay Disaster Response & Donation Tracking System

Tech Stack: Laravel (Backend + Blade Frontend) · MySQL · Lisk Blockchain (Planned Integration)
Developed for: Hackathon Project — CTU CCICT BAYANIHANCEBU Initiative
Goal: Build a transparent, real-time disaster response coordination and blockchain-verified donation tracking system for local government units.

🎯 Overview
🧩 What is BAYANIHANCEBU?

BAYANIHANCEBU is a centralized Disaster Response and Donation Tracking System built using Laravel, Blade, and MySQL.
It bridges communication between Local Disaster Risk Reduction and Management Offices (LDDRMO) and Barangay Disaster Risk Reduction and Management Committees (BDRRMC) — enabling faster response, resource sharing, and transparent donations through Lisk blockchain integration.

⚡ Core Vision

To empower city and barangay disaster offices with real-time visibility, efficient coordination, and verified donation transparency — eliminating delays, silos, and mistrust in community-driven relief operations.

🚨 Problem Context
Issue	Description
Response Delay	Disaster coordination is often slow, relying on manual or top-down communication.
Visibility Gap	Barangays lack awareness of which nearby areas have available resources.
Donation Opacity	Citizens cannot verify how or where donations are used.
Information Silos	No single source of truth for ongoing needs, offers, and disaster statuses.

🧭 Project Objectives
1. Centralize barangay disaster data and communication.
2. Coordinate real-time resource matching between barangays via the city’s LDDRMO.
3. Verify monetary donations using blockchain transactions through Lisk SDK.
4. Visualize barangay conditions and requests through an interactive map dashboard.

📋 MVP Scope (15 Days)
✅ IN SCOPE
-Authentication: LDDRMO & BDRRMC login with role-based permissions
-Map Dashboard: Publicly viewable disaster status per barangay
-Resource Coordination: Post, view, and match needs/offers
-Donation System: Allow residents to send monetary donations
-Blockchain (Planned): Record and verify donations on Lisk Testnet
-Pilot Coverage: 10–15 barangays within one city cluster

🚫 OUT OF SCOPE (Post-MVP)
-Full city rollout (1000+ barangays)
-In-kind donation tracking
-Mobile application
-Real-time messaging
-Automated matching algorithms

🏗️ System Architecture
 ┌────────────────────────────┐
 │        Residents           │
 │  - Donate (Monetary)       │
 │  - View Map Dashboard      │
 └─────────────┬──────────────┘
               │
               ▼
 ┌────────────────────────────┐
 │         BDRRMC             │
 │  - Login & Manage Needs    │
 │  - Post Resource Offers    │
 │  - View Matched Donations  │
 └─────────────┬──────────────┘
               │
               ▼
 ┌────────────────────────────┐
 │          LDDRMO            │
 │  - View All Barangays      │
 │  - Match Needs ↔ Offers    │
 │  - Approve Donations       │
 └─────────────┬──────────────┘
               │
               ▼
 ┌────────────────────────────┐
 │       Lisk Blockchain      │
 │  - Verify Transactions     │
 │  - Log Donation Records    │
 └────────────────────────────┘

⚙️ Technology Stack
**Layer**	                       **Technology**
Backend Framework	                 Laravel 11
Frontend Rendering		             Laravel Blade Templates
Database			                 MySQL (Workbench for schema & ERD)
API Layer			                 Laravel API Routes (RESTful)
Authentication			             Laravel Sanctum (Token-based)
Mapping Library		                 Leaflet.js (Barangay map visualization)
Blockchain (Planned)			     Lisk SDK (JavaScript – Testnet)
Deployment			                 Railway / Heroku (Backend) · Vercel / Netlify (Frontend)

**🧱 Database Schema (Simplified)
Table	Description
users	Stores user accounts with roles (lddrmo, bdrrmc, resident)
barangays	Contains barangay information: name, coordinates, status, and contact info
resource_needs	Records barangay requests for specific resources
resource_offers	Lists available resources for sharing
donations	Tracks donations with blockchain transaction hash
transaction_logs	Records blockchain activity for audit trail

Relationships:

users.barangay_id → barangays.id

resource_needs.barangay_id → barangays.id

donations.target_id → barangays.id**

🔐 Authentication Flow (Laravel Sanctum)
1. User logs in with email and password.
2. Backend validates credentials using bcrypt hashing.
3. On success, a Sanctum token (24-hour validity) is generated.
4. Frontend stores the token securely in localStorage.
5. Role-based middleware restricts access:
    - /lddrmo-dashboard → LDDRMO only
    - /bdrrmc-dashboard → BDRRMC only
6. Unauthorized access returns 403 Forbidden.

🌍 Core API Endpoints
Endpoint	                      Method          	              Description	                                          Access
/api/login	                        POST          	              Authenticate user and issue token	                      Public
/api/logout	                        POST	                      Revoke active token	                                  Authenticated
/api/barangays	                    GET	                          Fetch all barangay info	                              Public
/api/resource-needs                 GET/POST/PATCH	              Manage barangay needs                              	  BDRRMC, LDDRMO
/api/resource-offers	            GET/POST/DELETE	              Manage barangay offers	                              BDRRMC, LDDRMO
/api/match-resources	            POST	                      Match needs and offers	                              LDDRMO
/api/donations                      POST	                      Create new donation and trigger Lisk transaction	      Resident
/api/verify-donation/{tx_hash}	    GET	                          Verify blockchain transaction	                          Public

🗺️ Frontend Features (Blade Templates)
Page	                    Description
Login Page	                Role-based login (LDDRMO / BDRRMC)
Public Dashboard	        Map view of all barangays using Leaflet.js
LDDRMO Dashboard	        Two-column interface for resource matching
BDRRMC Dashboard	        Manage resource needs and offers
Donation Page	            Donation form with blockchain verification
Barangay Modal	            Popup with barangay details and needs list

🔗 Blockchain Integration (Lisk SDK)
Status: Planned (for Phase 2)

Goal	                              Implementation Plan
Setup Lisk Testnet		              Install Lisk SDK and create a test wallet
Record Donations		              Trigger transaction after donation record is created
Data Fields		                      Donor, Recipient, Amount, Timestamp, tx_hash
Verification	                      Endpoint /api/verify-donation/{tx_hash} retrieves blockchain confirmation
Reliability	                          Implement retry for failed or pending transactions

🧪 Testing Plan
Test Case	               Description
BDRRMC Workflow	           Login → Post Need → Verify Display on Map
LDDRMO Workflow	           Login → View Needs & Offers → Match Resources
Resident Workflow          Select Barangay → Donate → Verify Blockchain Tx
Error Handling	           Invalid login, expired token, role violations
Security	               SQL injection protection, rate limiting (max 5 login attempts/15 mins)

🚀 Deployment Guide
1. Backend Setup
**git clone https://github.com/your-repo/bayanihan.git
cd bayanihan
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
**
2. Frontend Setup
Laravel Blade is integrated directly into the backend:
**npm install
npm run dev
**

3. Environment Configuration (.env)
APP_NAME=BAYANIHANCEBU
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxxxx
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bayanihancebu
DB_USERNAME=root
DB_PASSWORD= ##(your dbms password)##
LISK_API_URL=https://testnet.lisk.io/api

4. Deployment
Layer	        Service
Backend	        Railway / Heroku
Frontend	    Vercel / Netlify
Database	    Cloud MySQL (Railway / PlanetScale)

📄 Documentation Deliverables
✅ README.md (System documentation)
✅ ERD Diagram (MySQL Workbench export)
✅ API Documentation (Postman collection)
✅ Demo Video (10-minute presentation)
✅ Slide Deck (Problem, Solution, Tech Stack, Demo)

👥 Development Team
Name	                    Role                    	    Responsibilities
Chelsie Faith B. Maranga	Project Manager / QA	        Documentation, Testing, Demo Prep
Carl Rey P. Tibon	        Database / Backend Developer	Schema Design, Models, Role-based Access
Judd H. Jala	            Backend Developer	            API Development, Authentication, Blockchain
Jan Louise V. Baroro    	Frontend Developer	            Blade UI, Dashboards, Map Integration

🌱 Future Enhancements
Real-time chat between barangays
Mobile version (Flutter or React Native)
In-kind donation logistics module
AI-driven disaster prediction
SMS / Email alert system integration

🏆 Hackathon Context

This project was developed for a blockchain innovation hackathon, powered by Lisk and ETH Philippines.
Our mission: “To build a transparent, community-driven platform that enables disaster resilience through digital collaboration.”

🪪 License / Usage Notice

© 2025 Fourloop() Hackathon Team

This project is developed exclusively for hackathon and demonstration purposes.
Anyone may fork, improve, or reference this repository for educational and non-commercial innovation projects, provided proper attribution to Team Fourloop() is retained.

“Built with teamwork, passion, and the spirit of Bayanihan.”

