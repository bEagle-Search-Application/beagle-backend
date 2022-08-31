# Linux installation

## Clone repository

To start to work with Beagle Api project, first, you must clone the repository with this command:
```
git clone https://github.com/bEagle-Search-Application/beagle-backend.git
```

We work on develop branch, so you can change the branch with:
```
git checkout develop
```

Now, enter the project with:
```
cd beagle-backend
```


## Set up project

To up all containers projects, execute:
```
docker/up
```

This command up all containers, execute database migrations and seeder.


## Execute Artisan Commands

If you wanna execute Artisan command, execute:
```
docker/dev/artisan "[your-command-here]"
```
