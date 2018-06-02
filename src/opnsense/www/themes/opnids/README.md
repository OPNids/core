# OPNids Theme

To build OPNids assets your local machine should have `node` installed. To build run the following command:

```sh
npm run build
```

The build script does the following activities:

* installs build pkgs temporarily
* copy over image assets
* copy over font assets
* compile SASS files
* compile LESS files
* removes build pkgs (`node_modules`) directory