Noma Solutions - Project Skeleton
=================================

Introduction
------------

This project is an extension for the official [Symfony Skeleton](https://github.com/symfony/skeleton) 
(recommended way for starting new projects using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html)). 

Out of the box it contains:

- Additional composer packages
- PHP Source placed in `src/backend`
- Source divided into 3 layers (Application, Domain, Infrastructure)
- Automated tasks based on [Robo](http://robo.li)
- Docker configuration for development environment
- Deployment scripts based on Ansible

@TBD

Creating new project 
--------------------

Creating new project with skeleton is as easy as running

```bash
composer create-project --no-cache -s dev noma-solutions/project-skeleton  <project_name> 
```

where `<project_name>` is the directory where you want to setup a new project. New project is ready for development 
immediately after this step.
 
