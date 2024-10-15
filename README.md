![Eventula Logo](resources/images/eventula_sample_logo.png)

> **Announcement**
> The development on Eventula will follow new paths in the future, since we forked off. This also means current users maybe have to take action. Read everything about it in our [related Issue](https://github.com/Lan2Play/eventula-manager/issues/842)


The Eventula Event Manager / ECO System is a fully featured White labeled Event Management system. The only prerequisite is `docker and/or docker-compose`. Everything is self contained. The purpose of this application is to unify Event Management (Venue, attendees, seating), Ticket Sales, Tournament Management, Shop Management and Credit Management. It was initially forked off of [th0rn0`s](https://github.com/th0rn0) [lanops-manager](https://github.com/th0rn0/lanops-manager).

<!-- commented out because of the downtimes -->
<!-- https://eventula.com -->

You can find the full documentation on https://eventula.lan2play.de .

If you need help with setting up or using eventula or you want to help developing or translating, join our discord:


[![Discord](https://discordapp.com/api/guilds/748086853449810013/widget.png?style=banner3)](https://discord.gg/zF5C9WPWFq)

<!-- commented out because of the downtimes -->
<!-- If you are using this please consider signing up to eventula for event mapping. -->

> **Warning**
> This fork of eventula manager is not 100% update compatible from [th0rn0`s](https://github.com/th0rn0) [lanops-manager](https://github.com/th0rn0/lanops-manager) or old versions of eventula-manager. If you migrate from one of those to our fork, please backup youre database and storage before doing so and please report any issues you encounter with the upgrade. We don't fully support this path nowadays, so expect problems to arise.


## Usage

> **Warning**
> Don't use any of the the ``docker-compose.yml`` or ``env.example`` from the repositorys root to run eventula in Production! You can find everything in the [Documentation](https://eventula.lan2play.de/admin/getting_started.html).


### local test or development
Hop over to our [developer documentation](https://eventula.lan2play.de/dev/getting_started.html) to see how to run eventula locally.

### production use
Hop over to our [admin documentation](https://eventula.lan2play.de/admin/getting_started.html) to see how to run and use eventula.


## Tanslation

[![Translation status](https://translate.lan2play.de/widgets/eventula-manager/-/multi-auto.svg)](https://translate.lan2play.de/engage/eventula-manager/)

## Screenshots
##### Home Page:

![Manager front page](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/home-page.png)
##### Event Page:
![Event Page](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/events-page.png)
##### Successful Payment Page
![Successful Payment](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/success-payment-page.png)
##### Event Management
![Event Admin Page](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/events-management-page.png)
##### Ticket Management
![Tickets Breakdown](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/tickets-management-page.png)
##### Tournament Management
![Tournament Management](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/tournaments-management-page.png)
##### Tournament Brakcets
![Tournament Brackets](https://raw.githubusercontent.com/Lan2Play/eventula-manager/master/resources/images/tournaments-brackets-page.png)

## Features

- White Label
- Event Management
  - Online and presence events
  - Signup/Info Pages
  - Tickets
  - Signups/Gifts/Staff/Refunds
  - Timetables
  - Tournaments
  - Seating
- Multiple Login Gateways
- Ticket Management
  - Sale Periods
  - Limited Quantity
  - Weekend, Day, Spectator Ticket Types
  - Eligible/Non Eligible for seats Ticket Type supported!
  - QR Codes supported!
- Seating Plans
  - Multiple Plans per Event supported!
  - Manual seating of participants
- Shop
- Credit System
- Venue Management
- User Management
- Timetable Management
  - Multiple timetables per event supported!
- Tournament Management
  - Supported via Challonge API
  - 1v1, Teams and PUGs supported!
  - Single/Double Elimination and Round Robin supported!
  - Different rules for each Tournament
- Matchmaking feature
- Event Sign in
  - Sign in via QR Code
- Event specific page for when at the event
  - Shows Timetables, Attendees, Tournaments, Announcements and Seating
- Game Management
  - Gamserver Management
    - RCON Support for remote management and status gathering (currently only for Maniaplanet (and Trackmania), Source and Goldsource)
    - Support for automated Gameserver assignments and fully automated matchmaking / tournaments (currently only Get5 for CS:GO is supported via https://github.com/Lan2Play/get5_eventula_apistats)
    - Public gameserver list on the homepage
- (File)Gallery
- Helpsystem to publish help/faq articles
- Admin Interface
- Web based CSS Editor
- Light and Dark Theme out of the Box
- Expandable templating and CSS System based on Bootstrap
- API Endpoints
  - Events
  - Participants
  - Seating
  - Timetables
- Payments
  - Terms & Conditions
  - Paypal Express
  - Stripe Card payments
  - Free payment provider
  - Breakdowns in Admin
- Voting and Poll Management
- News Management
- Comment Management
- Account Management
- Newsletter / Email Feature
- Multilanguage (currently EN and DE)
- EU cookie consent
- Imprint / Dataprotection page
- Fully Encapsulated in Docker Containers
- Built on Laravel
- Easily Expandable
- NGINX, MYSQL & PHP Docker stack



## Sites that use the Lan2play Eventula Manager

- Lan2Play - https://lan2play.de
- Sund-Xplosion - https://sxlan.de

## Credits
- [th0rn0](https://github.com/th0rn0) for the nice work on our Projects base [lanops-manager](https://github.com/th0rn0/lanops-manager)