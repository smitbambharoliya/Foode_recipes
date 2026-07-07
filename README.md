# Foode Recipes

## 📖 Project Title & Description

**Foode Recipes** is a robust culinary platform built to connect food enthusiasts and chefs. It provides an intuitive interface for chefs to share their culinary creations and for users to discover diverse recipes from around the globe. 

The application allows users to explore recipes based on meal types, dietary preferences (veg/non-veg), and regional cuisines. Chefs have a dedicated dashboard to create, edit, and manage their recipe portfolios, complete with image uploads and detailed ingredient lists.

## ✨ Key Features

- **User & Chef Authentication:** Secure login and registration system with distinct roles for general users and chefs.
- **Recipe Management:** Chefs can effortlessly add new recipes, upload food imagery, define servings, and provide step-by-step instructions.
- **Categorization & Filtering:** Recipes are categorized by meal type (e.g., Breakfast, Lunch, Dinner), region/cuisine, and dietary preference (Vegetarian or Non-Vegetarian).
- **Dynamic Form Handling:** Utilizes Data Transfer Objects (DTOs) and Symfony Forms for secure and robust data validation during recipe creation and editing.
- **Admin Dashboard:** Integrated with EasyAdmin for streamlined management of the platform's data.

## 🛠 Tech Stack

- **Backend Framework:** Symfony 7.4 (PHP 8.2+)
- **Database:** MySQL / MariaDB (via Doctrine ORM 3)
- **Templating Engine:** Twig
- **Admin Panel:** EasyAdmin Bundle 5
- **Assets:** Symfony AssetMapper
- **Form Handling:** Symfony Forms with strict validation constraints

## 🚀 Installation & Setup

Follow these steps to get the project running on your local machine:

1. **Clone the repository** (if applicable) or navigate to the project directory:
   ```bash
   cd d:\xamppa\Foode_recipes
   ```

2. **Install dependencies** using Composer:
   ```bash
   composer install
   ```

3. **Configure Environment Variables:**
   - Copy the `.env` file to `.env.local` if it doesn't exist.
   - Update the `DATABASE_URL` in `.env.local` with your local database credentials.
   ```env
   DATABASE_URL="mysql://root:@127.0.0.1:3306/foode_recipes?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
   ```

4. **Create the Database and Run Migrations:**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Start the Local Development Server:**
   ```bash
   symfony server:start
   ```
   The application will be accessible at `http://127.0.0.1:8000`.

## 🗄 Database Architecture

The database is designed around several core entities managed by Doctrine ORM:

- **User / Chef:** Handles authentication credentials, roles, and profile information. 
- **Recipe:** The central entity storing recipe details such as title, instructions, base servings, meal type, veg/non-veg flag, and an image path.
- **Region:** A taxonomy entity representing the geographical origin or cuisine style of a recipe (e.g., Italian, Indian, Mexican). Recipes hold a Many-to-One relationship with Regions.
- **Ingredients (JSON/Array):** Stores the required ingredients for a recipe, associated with the recipe record.

*(Note: Ensure you regularly back up your database and run `php bin/console doctrine:schema:validate` to keep your mapping and database in sync.)*
