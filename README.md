# auth-service
Repository for register &amp; auth users.
Also track events to external analytic service.
# how to start
- install 
[docker](https://docs.docker.com/install/)( 
[mac](https://docs.docker.com/docker-for-mac/),
[windows](https://docs.docker.com/docker-for-windows/)
) with 
[docker-compose](https://docs.docker.com/compose/install/)
- make sure that **80** port in **127.0.0.1** interface is free
- in the project dir run the next commands:
```bash
make up
open http://localhost
```


### Rebuild images & recreate containers
```bash
make build
make up
```

### To enable hot code refreshing in container follow instructions in ./docker-compose.yml


### Run test
```bash
./test
```

### RabbitMQ management
[http://localhost:15672/](http://localhost:15672/) **[guest:guest]**

#API documentation:

### Authorization
Request:
- Base url:
[http://localhost/login](http://localhost/login)
- Headers:
    - Content-Type: application/json
- Body:
```json
{
	"username": "test234",
	"password":"password"
}
```
or
- Headers:
    - Content-Type: application/x-www-form-urlencoded
- Body:
```params
username=test234&password=password
```
Response:
- success
```json
{
  "user": "ea2e7189-725c-41ad-af0a-dbe3a2efe99a"
}
```
- fail
```json
{
  "last_username": "test2344",
  "error": "Username could not be found."
}
```


### Registration
Request:
- Base url:
[http://localhost/register](http://localhost/register)
- Headers:
    - Content-Type: application/json
- Body:
```json
{
	"birthday": "1995/08/01",
	"firstname": "Ivan",
	"lastname": "Ivanov",
	"password": "password",
	"username": "ivan5"
}
```
or
- Headers:
    - Content-Type: application/x-www-form-urlencoded
- Body:
```params
username=ivan&password=password&lastname=Ivanov&firstname=Ivan&birthday=1995/08/01
```
Response:
- success
```json
{
  "data": "940008b2-e0a5-4f62-93aa-16c407d3478e"
}
```
- fail
```json
{
  "errors": {
    "username": [
      "The value \"ivan\" already exists."
    ]
  }
}
```
- fail
```json
{
  "errors": {
    "username": [
      "The value \"ivan5\" already exists."
    ],
    "firstname": [
      "This value should be of type alpha."
    ],
    "lastname": [
      "This value should be of type alpha."
    ]
  }
}
```


### Track
Request:
- Base url:
[http://localhost/track](http://localhost/track)
- Headers:
    - Content-Type: application/json
- Body:
```json
{
	"name": "register"
}
```
or
- Headers:
    - Content-Type: application/x-www-form-urlencoded
- Body:
```params
name=register
```
Response:
- success
```json
{
  "status": "OK",
  "data": {
    "name": "register"
  },
  "code": 200
}
```
- fail
```json
{
  "status": "Error",
  "data": {
    "name": "register"
  },
  "code": 500
}
```

Storage:
- users
/storage/users
```dotenv
STORAGE_USERS_PATH=%kernel.project_dir%/storage/users
```

- track
/storage/track
```dotenv
STORAGE_TRACKS_PATH=%kernel.project_dir%/storage/track
```

Message broker:
```dotenv
MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbitmq:5672/%2f/messages
```
