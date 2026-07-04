# 🏨 Hotel Room Booking System

A premium hotel room booking platform backend built with **Laravel 13**, **Vue 3 (Inertia.js v3)**, **Vite**, **TailwindCSS**, and **TypeScript**. It utilizes **Redis** for sessions/caching/queues and **MySQL** for data persistence.

---

## 🛠️ Prerequisites

Before you start, ensure you have the following installed depending on your preferred workflow:

### A. Docker Setup (Recommended 🐳)
* **Docker** & **Docker Compose**
* *Nothing else needed! All dependencies, runtimes, and databases are containerized.*

### B. Native Local Setup (Without Docker 💻)
* **PHP 8.4+** (with standard extensions: `pdo_mysql`, `redis`, `mbstring`, `zip`, `gd`, `pcntl`)
* **Composer 2+**
* **Node.js 22+**
* **MySQL 8.0+**
* **Redis** (running locally)

---

## 🚀 Getting Started

Choose one of the two setups below to get the project up and running:

### Option 1: Docker (Easiest) 🐳

1. **Clone the repository** and navigate to the project directory:
   ```bash
   git clone <repository-url>
   cd hotel-room-booking
   ```

2. **Run the Docker Setup Command**:
   ```bash
   make docker-setup
   ```
   *This single command will build the Docker images, start the containers, run the migrations, seed the database with mock rooms and users, and compile the frontend assets.*

3. **Access the application**:
   * Web App: [http://localhost:8000](http://localhost:8000)
   * MySQL Port: `3306` (username: `hotel`, password: `secret`, database: `hotel_booking`)
   * Redis Port: `6379`

---

### Option 2: Native Local Development (Without Docker) 💻

1. **Clone the repository** and navigate to the project directory.

2. **Configure environment variables**:
   * Copy the example environment file:
     ```bash
     cp .env.example .env
     ```
   * Open `.env` and configure your local database (`DB_HOST=127.0.0.1`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and Redis (`REDIS_HOST=127.0.0.1`).

3. **Install dependencies and compile assets**:
   ```bash
   make setup
   ```
   *This executes Composer installation, generates the app key, runs migrations, installs npm packages, and builds the frontend bundle.*

4. **Seed the database (Optional but Recommended)**:
   ```bash
   php artisan db:seed
   ```

5. **Start development servers**:
   ```bash
   make dev
   ```
   *This starts the Laravel local serve, queue listener, Pail logging, and Vite development server concurrently.*

---

## 🗂️ Project Commands (Makefile Targets)

A `Makefile` is included to make running common tasks easier. Run `make help` to view this list:

### Docker Commands
| Command | Description |
| :--- | :--- |
| `make up` | Start all Docker containers in background |
| `make down` | Stop and remove all containers, networks, and volumes |
| `make build` | Build/rebuild Docker service images |
| `make restart` | Restart all running containers |
| `make ps` | List all running Docker containers |
| `make logs` | Tail logs of all services (or e.g., `make logs svc=app`) |
| `make shell` | Start a shell session inside the `app` container |
| `make db-fresh` | Re-run migrations and seeds inside the container |
| `make docker-setup` | Full setup from scratch inside Docker (Build, Up, Seed, Wayfinder) |
| `make docker-test` | Run PHPUnit tests inside the container |
| `make docker-pint` | Run Laravel Pint formatter inside the container |
| `make docker-wayfinder` | Generate Wayfinder TypeScript routes inside the container |

### Native Local Commands
| Command | Description |
| :--- | :--- |
| `make setup` | Run first-time setup locally (composer & npm install, asset build) |
| `make dev` | Start development servers concurrently (PHP, Vite, Queue, Pail) |
| `make test` | Run PHPUnit test suite locally |
| `make lint` | Run Laravel Pint check locally |
| `make lint-fix` | Formats PHP files locally using Pint |
| `make format` | Formats Vue/TS/CSS code with Prettier |
| `make format-check` | Runs Prettier format verification checks |
| `make types-check` | Runs Vue-TSC compilation checks |
| `make clean` | Safely clear Laravel configuration, routes, and views cache |

---

## 🧪 Testing & Code Quality

This project enforces strict unit and feature testing along with style guide standards:

* **Running Tests**:
  * Local: `make test`
  * Docker: `make docker-test`
* **Checking PHP Code Style**:
  * Local: `make lint`
  * Docker: `make docker-pint`
* **Formatting PHP Code**:
  * Local: `make lint-fix`
* **Formatting Frontend Code**:
  * Local: `make format`
