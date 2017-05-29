# Supervisor install 

Installation of Supervisor on both Ubuntu and Debian is incredibly simple, as prebuilt packages already exist within both distributions' repositories.

As the root user, run the following command to install the Supervisor package:

```apt-get install supervisor```

# Adding a program

The program configuration files for Supervisor programs are found in the /etc/supervisor/conf.d directory, normally with one program per file and a .conf extension. A simple configuration for our script, saved at /etc/supervisor/conf.d/long_script.conf, would look like so:

```
[program:lpq-worker]
process_name=%(program_name)s_%(process_num)02d
command=php path_to_artisan worker:receiver
autostart=true
autorestart=true
user=username
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/queues/lpq-worker.log
```
