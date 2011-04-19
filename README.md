## Lagged_Munin

This is a set of helpers to create a munin plugin. So simple.

Check out `examples/` for two examples on how to use this code.

## How does it work?

### You need munin and munin-node:

    sudo aptitude install munin munin-node

### How to use the example plugins:

 1. clone this repository
 2. symlink: `sudo ln -s /path/to/Lagged_Munin/examples/load.php /etc/munin/plugins/load`
 3. `sudo restart munin-node`


