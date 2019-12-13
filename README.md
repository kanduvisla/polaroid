# Polaroid

A small utility to create polaroid-like picture from ordinary JPG images.

## Usage

Use the accompanied `Dockerfile` to build the Docker image:

    docker build -t polaroid .

To see it in action, run:

    (tbd)

If you want to spin up a container for development / debugging purpose, run:

    docker run --rm -i
        -u $(id -u ${USER}):$(id -g ${USER})
        -w /app
        -v ${PWD}:/app
        -t polaroid
