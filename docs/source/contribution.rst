
Contribution Guide
==================================================

We are looking forward to every conbtirbution you possibly can bring in.

If you encounter serious errors while using eventula and you are not able to fix them, feel free to open issues on https://github.com/Lan2Play/eventula-manager/issues

If you plan to add a feature to eventula, please open an issue as well so no one has to do work when someone already working on something.

You can also join our discord to get in contact with us and the other contributors:


.. image:: https://discordapp.com/api/guilds/748086853449810013/widget.png?style=banner3
   :target: https://discord.gg/zF5C9WPWFq


Documentation
--------------
This documentation is written in restructured text and its build using sphinx and the read the docs theme. The source can be found in our main repository in the ``docs/`` subfolder (https://github.com/Lan2Play/eventula-manager/tree/main/src).
Feel free to PR corrections or expansions of the documentation at any time! 

To build the documentation locally to the ``docs/build`` subfolder you have two options:

- Building with docker and the make file (recommended)
- Building with docker
- Building manually with the sphinx make file

Building with docker and the make file
.......................................

Makefile & docker - Windows
''''''''''''''''''''''''''''''''''''''''''
Prereqirements: 

- Docker for Windows with wsl2 backend (https://docs.docker.com/docker-for-windows/wsl/ Follow the Prerequisites, the Download and the Install part!)


 .. warning::

        If you are using git, consider cloning the repository within your wsl distro instead of with git for windows to get around line ending problems!

To build the documentation just enter yor wsl2 distribution and follow the Linux part below!


Makefile & docker  -Linux
''''''''''''''''''''''''''''''''''''''''''
Prereqirements: 

- Docker (https://docs.docker.com/engine/install)
- Make (should be available for nearly every linux distro in the corresponding packagemanager)

In order to build the documentation run

.. code-block:: bash

   make docs-html


Building with docker
.......................................

Docker Windows
'''''''''''''''''''''
Prereqirements: 

- Docker for Windows with wsl2 backend (https://docs.docker.com/docker-for-windows/wsl/ Follow the Prerequisites, the Download and the Install part!)


 .. warning::

        If you are using git, consider cloning the repository within your wsl distro instead of with git for windows to get around line ending problems!

To build the documentation just enter yor wsl2 distribution and follow the Linux part below!


Docker Linux
''''''''''''''''''''''''''''
Prereqirements: 

- Docker (https://docs.docker.com/engine/install)

In order to build the documentation run the following command inside the root of the cloned repository

.. code-block:: bash

   docker run --rm -v $PWD/docs:/docs lan2play/docker-sphinxbuild:latest

Building manually with the sphinx make file
............................................

Manual Windows
'''''''''''''''''''''
Prereqirements: 

- python 3 (https://docs.python.org/3/using/index.html) with pip
- sphinx (https://www.sphinx-doc.org/en/master/usage/installation.html) 
- the read the docs theme (https://github.com/readthedocs/sphinx_rtd_theme#installation)

open a cmd or powershell and change your folder to the ``docs/`` subfolder and run

CMD

.. code-block:: batch

   make.bat html

Psh

.. code-block:: powershell

   ./make.bat html


Manual Linux
'''''''''''''''''''''
Prereqirements: 

- python 3 (https://docs.python.org/3/using/index.html) with pip
- sphinx (https://www.sphinx-doc.org/en/master/usage/installation.html) 
- the read the docs theme (https://github.com/readthedocs/sphinx_rtd_theme#installation)
- Make (should be available for nearly every linux distro in the corresponding packagemanager)

open your favorite shell and change your folder to the ``docs/`` subfolder and run

.. code-block:: bash

   make html


Localisation
-------------
We try to implement the software and the documentation localable, but we currently can only do german and english translations, therefore any help is appreciated!

Documentation localisation
..........................
Todo!

Eventula localisation
.....................

translation status
'''''''''''''''''''''
.. image:: https://translate.lan2play.de/widgets/eventula-manager/-/multi-auto.svg
    :alt: Translation status
    :target: https://translate.lan2play.de/engage/eventula-manager/

translate with weblate
'''''''''''''''''''''''

You can find the client translation on our `Weblate project`_

.. _Weblate project: https://translate.lan2play.de/engage/eventula-manager/


translate without weblate 
'''''''''''''''''''''''''''

You can find the localisation files in ``src/resources/lang/``. If you want to fix mistakes, you can find the files for every translated language in the corresponding subfolder.
If you want to add a whole language, copy the whole en folder and rename it to the Language code you want to add. The language files are Key - Value pair files, just edit the Value in there.

The localisations could be accessed in the PHP code with (example whoeweare from src/resources/lang/de/about.php):

.. code-block:: php

    __('about.whoeweare')

or within blade files (Views):

.. code-block:: php

    @lang('about.whoeweare')



Code
-----
If you want to get into coding for eventula, check out the :ref:`dev/getting_started:Getting Started` developer documentation, there you can find an introduction into how to setup your development environment and some specific parts of eventula where we would love to see adaption for more usecases.

Some things you should think of before starting out implementing new features:

- Can another feature thats already implemented be expanded? yes? then go for that instead of Building complete new stuff!
- Does the addition / change might affect other usecases than your own? Build your changes with legacy support in mind!
- Try to follow the coding Style which is used within eventula, just look around in our features to see which case is handled mostly in which manner
- Why i shouldn't join the eventula discord developer channel for discussion?  


To start a new code contribution please:

- open an issue to announce that you are working on a feature/change to get thoughts from the other developers and to prevent incompatibillities
- make your fork of eventula (if you are not already a acknowledged developer who can create branches on our repository)
- make a new branch based on ``main`` with the Name ``feature/examplefeature`` 
- as soon as you have code, please open a draft pull request against the ``main`` branch. 

Before you want to PR changes you should ask yourself some questions:


- Have i tried to update a running version from eventula with data to the one with my changes? Are the changes update proof?
- Have i changed the admin documentation on the affected parts?
- Have i implemented all strings with localized variables? See Localisation!
- Have i changed the developer documentation on the affected parts?
- Have i changed the readme.md on the affected parts?
- Does my pull request have a good speaking name that represents my changes in the changelog?
- Do i have merged the current ``main`` branch into my feature branch?

.. note::
   Please open your pull requests against our ``main`` branch. 

What will happen after you have converted to the finalized pull request:

.. - Someone of the core developer team assigns a specific label to your pull request, then our sonarcloud code analysis will run on your code. Afterwards please fix the things sonarcloud complains about.
.. - When your code passes the sonarcloud analysis, someone of the core developer team will review your code and will help you to find missing things or bugs.

- Someone of the core developer team will review your code and will help you to find missing things or bugs.
- As soon as the review is done, your code will be merged to main (where can be tested for a few days) and will get then released as soon as possible. Currently we make releases as they are required.