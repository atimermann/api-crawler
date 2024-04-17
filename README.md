# Crawler API

This project is a Crawler API is designed to fetch currency information based on ISO 4217 standard codes. This API
retrieves details
of various currencies by performing a crawl on an external data source using the standard's three-letter currency codes
or numeric identifiers.

## Getting Started with Docker

This project utilizes Docker and Docker Compose to create an isolated and consistent development environment. Follow the
instructions below to set up and start the project quickly.

### Prerequisites

Before starting, ensure that you have Docker with Docker Compose installed on your system. To install these tools,
follow the official guides:

- [Install Docker](https://docs.docker.com/engine/install/)
- **Make**: This project includes a Makefile that simplifies the process of running Bash scripts.

### Initializing the Project

To initialize the project, you'll need to build the Docker images and run the containers. Here is a step-by-step guide:

1. **Start the Containers**
   Once the build process is complete, you can start the containers using:
   ```bash
   docker compose up -d
   ```

2. **Accessing the Application**
    - The web server is available at `http://localhost:8000`
    - PHPMyAdmin is accessible at `http://localhost:8080`

3. **Stopping the Containers**
   When you are done, you can stop the Docker containers with:
   ```bash
   make down   
   ```

### Additional Commands

- To list available commands:
  ```bash
  make
  ```

- To enter the application container for executing commands or managing the application, use:
  ```bash
  make shell
  ```
- To view logs from the containers, use:
  ```bash
  make logs  
  ```
- To rebuild the containers after making changes to Dockerfiles or Docker configurations:
  ```bash
  make rebuild  
  ```

- To run mutation test:
  ```bash
  make infection
  ```

### Troubleshooting

If you encounter any issues with Docker, refer to the official Docker documentation or check the logs
using `docker-compose logs`. This can provide insights into what might be going wrong.

# Additional Project Details

* This project uses semantic commits: https://github.com/iuricode/padroes-de-commits
* It utilizes Symfony BrowserKit for crawling tasks
* Assumes that all codes and numbers are unique and not repeated; during the search, they are processed together without identifying whether it is one or the other.
* The project uses the POST method for its endpoint to submit data for web scraping tasks due to its ability to handle
  larger data payloads securely, non-idempotent behavior, and semantic alignment with data modification actions.

