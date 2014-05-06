# Installing Lithium

## Getting the Code

This easiest way to get a fresh copy of Lithium is by downloading an archive from our website. You can view and download versions here:

  https://github.com/UnionOfRAD/lithium/releases
  
Under the hood, Lithium is actually separated in two different repositories. One is called `framework` and the other one `lithium`. The `framework` repository holds everything you need to 
instantly bootstrap your application, while the `lithium` repository holds the Lithium core. This way you can reuse the Lithium core for other projects or just include some libraries if you 
need them. 

The repositories are hosted in GitHub, where you can also download tarballs if you just want to play around and not fetch updates through a managed repository. The normal process of fetching 
Lithium by source is to clone the `framework` repository and then install `lithium` as a submodule (which is already configured for you).

  git clone git://github.com/UnionOfRAD/framework.git my_app
  cd my_app
  git submodule init
  git submodule update
  
If everything worked as expected, you should now have the lithium core inside `my_app/libraries/lithium`. If you've downloaded the tarballs, make sure to unpack the core in the correct 
directory.

## Getting the most recent revision (optional)

The method described in the previous section will download the most recent tagged version of Lithium. In some cases, it may be desirable to update Lithium to the very latest available revision, which may not have been tagged yet.

  cd libraries/lithium
  git pull origin master

## Advanced Setup

If you've got a system that's hosting many Lithium apps, sometimes it's beneficial to point a number of applications at the same set of core Lithium libraries. In this case, you'd want to place Lithium somewhere outside of your web server's document root.

For example, let's say we've got lithium installed in `/usr/local/lib/lithium/` and some Lithium applications at `/home/apps/first-app/` and `/home/apps/second-app`.

The two applications mentioned aren't complete copies of the Lithium codebase: they're just copies of the `app` folder (e.g. there should be a `/home/apps/first-app/config` folder).

First, you'll want to create two virtual hosts for the applications on the system. Once those are in place, each application's bootstrap will need to be informed of where the Lithium libraries are. Adjust both application's `/config/bootstrap/libraries.php` file like so:

  define('LITHIUM_LIBRARY_PATH', dirname('/usr/local/lib/lithium'));

## Pedal to the Metal

For the purposes of this guide, we'll assume you're running Apache. Before starting things up, make sure mod_rewrite is enabled, and the AllowOverride directive is set to 'All' on the necessary directories involved. Be sure to restart the server before checking things.

Another quick thing to check is to make sure that magic quotes have been completely disabled in your PHP installation. If you're seeing an exception error message initially, you might have magic quotes enabled. For more information on disabling this feature, see the [PHP manual](http://www.php.net/manual/en/security.magicquotes.disabling.php).

While you're making PHP configuration changes, you might also consider having PHP display errors temporarily during development. Just change the relevant lines in your `php.ini`:

  error_reporting  =  E_ALL
  display_errors   =  On

Finally, pull up li3 in your browser. For this example, we're running Apache locally. Assuming you have a default configuration, and you cloned Lithium into your document root directory, you can visit [`http://localhost/lithium`](http://localhost/lithium).

At this point, you should be presented with the Li3 default home page. You're up and running!

## One More Thing

Lastly, you'll want to set up the `li3` command so it's easy to use as you move around your filesystem. The `li3` command assists in tasks like code generation, documentation, and testing.

To do so, add the Lithium's console library directory to your shell's path. For our example above, and assuming you're using the bash shell, you'd add something like the following to your `~/.bash_profile` file:

  PATH=$PATH:/path/to/docroot/lithium/libraries/lithium/console

Once this has been done, you can execute the li3 command inside the app folder of any Li3 app you have on your filesystem. If it's running successfully, you should get the following default usage output:

  USAGE
    li3 COMMAND [ARGS]

  COMMANDS
    create
      The `create` command allows you to rapidly develop your models, views, controllers, and tests
      by generating the minimum code necessary to test and run your application.

    g11n
      The `G11n` set of commands deals with the extraction and merging of
      message templates.

    test
      Runs a given set unit tests and outputs the results.

  See `li3 help COMMAND` for more information on a specific command.