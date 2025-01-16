# php-symfony-tp

**See live version at: https://cooksphere.onrender.com/**

Host with : https://render.com/

**Launch the project with docker.**

```
-(docker compose build)
```

```
-(docker compose up)
```

Now go to folder cook_sphere and do:

```
cd cook_sphere
composer install
```

After that you can go to cook_sphere folder and launch fixtures:

```
./launch-fixtures.sh
```

-Adminer is at localhost:8080 (see .env for mdp it's in postgresql)

-Front is at localhost:8000

-To see if email sent: localhost:8090

You can log with this user with role admin:

email: user1@example.com

password: password123

For user with role user:

email: user2@example.com

password: password123

For user with role banned:

email: user3@example.com

password: password123
