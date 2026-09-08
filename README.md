# Task API - Setup Instructions

## How to run locally
1. Clone the repository: `git clone https://github.com/AndresMonjaras/miapp.git`
2. Switch to branch: `git checkout api-tareas`
3. Configure database in `api/config/database.php`
4. Run the SQL in `api/database.sql` to create the tareas table
5. Start PHP server: `php -S localhost:8080 -t api/public/`
6. Access API at http://localhost:8080/api/tareas

## Swagger UI
Access interactive documentation at: http://localhost:8080/api-docs/

[Insert screenshot of Swagger UI]

## Postman Testing
Create a new task (POST /api/tareas) and list tasks (GET /api/tareas)

[Insert screenshot of Postman creating a task]
[Insert screenshot of Postman listing tasks]

## Production URL
API running at: http://topicosweb.celaya.tecnm.mx/22030873/miapp/api/public/
