## Install
```composer install```

```bin/console doctrine:migrations:migrate```

## Data Load
```bin/console doctrine:fixtures:load```

## Setup keys
```ssh-keygen -t rsa -b 4096 -m PEM -f jwt/jwtRS256.key``` - *Don't add passphrase*

```openssl rsa -in jwtRS256.key -pubout -outform PEM -out jwt/jwtRS256.key.pub```

I left in the code the keys I created, but I wouldn't leave it normally. 

## Testing
```bin/phpunit```

The functional testing was done using Postman Collection
https://www.postman.com/collections/77bd88d81b2f5d44e775