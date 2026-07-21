# Foode Recipes

Welcome to the **Foode Recipes** project! This is a modern backend application designed to manage recipes, ingredients, user reviews, and regional data, built with a robust REST API and an integrated administration dashboard.

## Overview

Foode Recipes serves as the backend engine for a recipe application. It handles user authentication, exposes endpoints to fetch recipe data, and provides a fully-featured admin interface for content management. 

### Key Features
- **RESTful API**: Exposes data endpoints (e.g., retrieving recipes) for frontend or mobile consumption.
- **Admin Dashboard**: An intuitive administrative panel to manage all core data (Users, Recipes, Ingredients, Reviews, Regions, and Recipe Views).
- **Authentication**: Secure JWT-based API authentication.
- **API Documentation**: Automated OpenAPI/Swagger documentation.

## Tech Stack

The project was built using the latest industry standards and frameworks:
- **Language**: PHP 8.2+
- **Framework**: Symfony 7.4
- **ORM**: Doctrine (with Migrations)
- **Admin Panel**: EasyAdmin Bundle 4+
- **Security**: LexikJWTAuthenticationBundle
- **API Docs**: NelmioApiDocBundle

## Core Entities (Database Architecture)

The system revolves around several core data models:
- **User**: Represents application users and administrators.
- **Recipe**: The core entity storing recipe details, instructions, and metadata.
- **Ingredient**: Components required for the recipes.
- **Region**: Categorizes recipes based on geographical or cultural regions.
- **Review**: User feedback and ratings on recipes.
- **RecipeView**: Tracks analytics and views for individual recipes.

## How This Was Built

### 1. Foundation
The project was initialized using the Symfony skeleton (`symfony/skeleton`). Essential packages like Doctrine, Security, and Maker bundles were added to form the foundation.

### 2. Database & Entities
Entities were generated to map the database structure using Doctrine attributes. Relationships were established between Recipes, Ingredients, Users, and Reviews. Database migrations were then executed to synchronize the schema.

### 3. API & Authentication
- Added `lexik/jwt-authentication-bundle` to handle secure API login.
- Built API controllers (e.g., `ApiRecipeController`) utilizing modern PHP 8 attributes (`#[Route]`) to serve JSON responses.
- Configured Nelmio ApiDoc to automatically document the available API endpoints.

### 4. Admin Dashboard
- Installed `easycorp/easyadmin-bundle` to rapidly build a back-office.
- Created `DashboardController` to act as the entry point and sidebar menu.
- Created individual CRUD controllers (`UserCrudController`, `RecipeCrudController`, etc.) to automatically generate tables and forms for managing database entities.
- Upgraded the EasyAdmin menu syntax to use the latest `MenuItem::linkTo()` method mapped directly to the generated CRUD controllers.

## Running the Project Locally

1. **Install Dependencies**: 
   ```bash
   composer install
   ```
2. **Environment Variables**: Configure your database connection and JWT secrets in the `.env.local` file.
3. **Run Migrations**:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
4. **Start the Development Server**:
   ```bash
   symfony server:start
   ```

## Changelog / Recent Changes
*This section tracks the ongoing changes made to the project.*

- **2026-07-20**: Initialized this README file.
- **2026-07-20**: Fixed `linkToCrud` deprecation in `DashboardController.php` by migrating to the new `MenuItem::linkTo(CrudController::class)` syntax.
- **2026-07-20**: Configured `form_login` in `security.yaml` and protected the `/admin` route so the login page works properly.
- **2026-07-20**: Modified `DashboardController::index` to redirect directly to the `RecipeCrudController` instead of showing the default EasyAdmin welcome page.
- **2026-07-20**: Fixed a Doctrine `[Semantical Error]` in `RecipeRepository` by updating queries to use the correct entity property `r.mealtype` instead of `r.meal_type`.
- **2026-07-20**: Fixed Twig error `Key "reviews" for sequence/mapping...` by fixing `findTrendingByTime` to select the recipe entity under the `recipe` key instead of index `0`.

*Enjoy building the Foode Recipes platform!*
