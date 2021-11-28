KeeWeb local server

You can use PASSWORD or OAUTH (gitlab) for access user for store

auth users by oauth

    mkdir store && chmod 0777 store && \
    docker run -p 8080:8080 \
    -e OAUTH_CLIENT=<gitlab oauth clientid> \
    -e OAUTH_SECRET=<gitlab oauth secret> \
    -e OAUTH_SERVER=<gitlab url> \
    -v $(pwd)/store:/opt/store mokinanton/keeweb-server:latest

or use password

    mkdir store && chmod 0777 store && \
    docker run -p 8080:8080 \
    -e PASSWORD=<strong passwords> \
    -v $(pwd)/store:/opt/store mokinanton/keeweb-server:latest


    