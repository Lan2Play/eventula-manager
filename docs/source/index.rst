.. Eventula Event Manager documentation master file, created by
   sphinx-quickstart on Tue Dec 22 03:54:45 2020.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

.. image:: images/eventula_sample_logo.png
   :scale: 50 %
   :alt: eventula logo
   :align: center

Welcome to Eventula's documentation!
==================================================

.. warning::

   This documentation is not fully up to date and not finished, so if you find something that is not right, consider contributing to it or open an issue. 

.. warning::

   This fork of eventula manager is not 100% update compatible to the current upstream. If you migrate from the upstream to our fork, please backup youre database and storage before doing so and please report any issues you encounter with the upgrade.


The Eventula Event Manager / ECO System is a fully featured White labeled Event Management system. 

The only prerequisite is docker and/or docker-compose. Everything is self contained. 

The purpose of this application is to unify Event Management (Venue, attendees, seating), Ticket Sales, Tournament Management, Shop Management and Credit Management.

.. commented out because of the downtimes
.. https://eventula.com

.. If you are using this please consider signing up to eventula for event mapping.

If you need help with setting up or using eventula, join our discord:

.. image:: https://discordapp.com/api/guilds/748086853449810013/widget.png?style=banner3
   :target: https://discord.gg/zF5C9WPWFq


.. toctree::
   :maxdepth: 1
   :caption: Contents:

   contribution
   license


.. toctree::
   :caption: Admin Documentation
   :maxdepth: 2

   admin/getting_started
   admin/events/basic
   admin/news
   admin/usermanagement
   admin/polls
   admin/venues
   admin/gallery
   admin/help
   admin/games/basic
   admin/matchmaking
   admin/purchases
   admin/settings/basic
   admin/mailing
   admin/credit
   admin/shop
   admin/tutorials/basic

   
.. toctree::
   :caption: Developer Documentation
   :maxdepth: 1

   dev/getting_started
   dev/folderstructure
   dev/developer_meeting
   dev/games/gamecommandhandler
   dev/games/gamematchapihandler
   
.. toctree::
   :caption: API Documentation
   :maxdepth: 1

   api/events
   api/matchmaking
   api/tournaments
   



Indices and tables
==================

* :ref:`genindex`
* :ref:`modindex`
* :ref:`search`
