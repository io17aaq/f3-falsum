#Fat-Free Framework - Visual Error Handler

ErrorHandler.php is a php class file to use with Fat-Free Framework, also known as F3. You can check what F3 is, [here](http://fatfreeframework.com/home), basicaly its a PHP microframework that although its a microframework, its very powerfull. In my opinion it just lacks of a better error handler, that can show errors in a more apelative and compreehensive way. This two files I provide in this package, can achieve that. I will provide a short tutorial on how to implement this, with the basic fat-free package on github. If you havy any doubts, you can use [Fat-Free Google Groups](https://groups.google.com/forum/?fromgroups#!forum/f3-framework), to ask for help. If I'm not around, I'm sure any expert in Fat-Free could help you. Feel free to join, its an amazing community.

![http://i.imgur.com/xuj1urc.jpg](http://i.imgur.com/xuj1urc.jpg)

Fig. 1 - Error Handler in action

##How to Implement this

###Step 1 - Download Files

First of all, you need to download the file located in the `dist/` folder on this repository. `ErrorHandler.php` thats the php class. Go ahead and download it.

###Step 2 - Paste Files

The second part its even easier than the previous one, just copy this file you've just downloaded into the same level that `base.php` file containing all Fat-Free core stuff. This is usually located on your root directory, than go to `lib/` folder. I have done it, in this way so your framework always loads it by itself, when loading all the other core components and classes.

###Step 3 - Calling your Class

On your `index.php` file, you have to call your class to active the new error handler. Simply paste the following line of code right before `$f3->run()`.

```php
if($f3->get('DEBUG') == 3) ErrorHandler::instance($f3);
```

What this will do, is check if your Debug global variable is on developer mode. If its true, calls the ErrorHandler class instantiation method with `$f3` as a parameter.

Et voilá, now it should be working and you get a better looking way to display your errors. I'm suspect for saying this, but since I'm using this, it helped me a lot many many times.
