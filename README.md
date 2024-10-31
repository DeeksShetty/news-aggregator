# News Aggregator Laravel Project

<p align="center">A comprehensive news aggregation platform built with Laravel and Docker.</p>

## Getting Started

This project is configured to run with Docker using Laravel Sail. Follow the steps below to set up and run the application.

### Prerequisites

- **WSL or Ubuntu** installed on Windows (if using Windows)
- **Docker Desktop** installed on your system

### Installation

1. **Clone the Repository**  
   Ensure you are inside WSL or Ubuntu if you are using Windows.

2. **Build the Docker Containers**  
   Run the following command from the project root directory to build the Docker containers (this may take a few minutes):
   ```bash
   ./vendor/bin/sail build

3. **Start the Containers**  
   Once the build is complete, start the containers with:
   ```bash
   ./vendor/bin/sail up
   
   You can now view running containers and images in Docker Desktop.

4. **Access the Application**  
   The application should now be accessible at http://localhost:8083.
   
5. **Run Database Migrations**  
   Create the required tables by running:
   ```bash
   ./vendor/bin/sail artisan migrate

6. **Seed the Database**  
   Populate the database with initial data:
   ```bash
   ./vendor/bin/sail artisan db:seed

7. **Run Scheduled Article Fetch**  
   Fetch articles from APIs (e.g., Guardian API, News API, New York Times API) with:
   ```bash
   ./vendor/bin/sail artisan schedule:run

8. **Explore the API Documentation**  
   Visit the documentation link to view and test all available APIs and their details.
   

## Project Information

- **Site URL:** [http://localhost:8083](http://localhost:8083)
- **API Base URL:** [http://localhost:8083/api](http://localhost:8083/api)
- **API Documentation URL:** [http://localhost:8083/api/documentation](http://localhost:8083/api/documentation)
- **Run Tests:** Run the following to execute test cases:
  ```bash
  ./vendor/bin/sail artisan test
