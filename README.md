# ELO Generator

This project is a web application designed to automate and manage Course Learning Outcomes (CLOs) and educational documents. It is proudly built using the [Laravel Framework](https://laravel.com) and integrates with **Ollama** for robust, local Artificial Intelligence processing.

---

## 🚀 Getting Started

Follow these instructions to set up the project on your local machine for development and testing purposes.

### Prerequisites

Ensure your local development environment meets the following requirements:
* PHP & [Composer](https://getcomposer.org/)
* [Node.js & npm](https://nodejs.org/)
* [Ollama](https://ollama.com/download) (For local AI generation)

---

### 1. Application Setup

First, install all necessary PHP and Node.js dependencies, and compile the frontend assets:

```bash
composer install
npm install
npm run build
```

Next, create a symbolic link for the storage directory to ensure public accessibility for documents and uploaded files:
```bash
php artisan storage:link
```

### 2. Local AI Engine Setup (Ollama)
This application relies on a custom local AI model named AI-Assisted CLO. Please follow these steps to configure and run the model:

1.Install Ollama: Download and install Ollama from the [official website](https://ollama.com/download/). For more details, refer to the [Ollama Documentation](https://github.com/ollama/ollama/blob/main/README.md).

2.Navigate to the Model Directory:
```Bash
cd .\ollama\
```
3.Build the Custom Model:
Compile the model using the provided Modelfile inside the directory.
```Bash
ollama create elo_generator -f Modelfile
```
4.Run the Model:
Start the model so it can run in the background and accept API requests from the Laravel application.
```Bash
ollama run elo_generator
```

### 3. System Optimization & Cache Management
If you experience configuration caching issues, make changes to the environment variables, or add new classes, run the following commands to clear the application cache and optimize the system:
```Bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

### 4. Running the Application (Daily Usage)
Whenever you want to start or work on the project, open your terminal and run the following commands:

Step 1: Start the local AI model (Keep this terminal open)
```Bash
ollama run elo_generator
```

Step 2: Open a new terminal window and start the Laravel development server
```Bash
composer run dev
```

Note: If you encounter any UI, styling, or frontend-related errors, you may also need to start the Vite development server by running this command in a separate terminal:
```Bash
npm run dev
```

# License
This project is built on the Laravel framework, which is open-sourced software licensed under the [MIT license](https://opensource.org/license/MIT).