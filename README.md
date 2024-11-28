## Laravel Multi-Authentication Using Guards (No Package)

This project demonstrates a simple and efficient implementation of multi-authentication in Laravel using guards. By leveraging Laravel's built-in authentication system, we provide a way to authenticate multiple user types (e.g., admin, user etc.) without relying on third-party packages.

### Features

-- [Separate Guards: Each user type has its own guard for authentication.]
-- [Role-Specific Dashboards: Users are redirected to their respective dashboards upon login.]
-- [Middleware Protection: Middleware is used to protect routes specific to each user type.]
-- [Custom Login Views: Custom login forms for different user types.]
-- [Easy Extendability: Add new user roles and guards without major changes to the codebase.]

### Key Concepts

-- [Guards: Define how users are authenticated for each user type.]
-- [Providers: Specify the database tables and models for each user type.]
-- [Middleware: Secure routes based on user roles and guards.]

### How It Works

-- [Configuration: Guards and providers are configured in config/auth.php.]
-- [Models: Each user type has its own model and table in the database (e.g., Admin, User).]
-- [Middleware: Role-specific middleware ensures that only authenticated users of a specific type can access certain routes.]
-- [Routing: Routes are grouped and protected by middleware for each user type.]
-- [Login Logic: Custom login logic is implemented in the controllers for each user type.]
