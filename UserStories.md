
# Eventula Manager - User Stories

## Overview
This document contains comprehensive user stories for the Eventula event management system, organized by user persona. Each story follows the format: "As a [persona], I want [goal], so that [benefit]."

---

## User Personas

1. **Guest/Visitor** - Non-authenticated user browsing the site
2. **Participant** - Registered user attending events
3. **Tournament Player** - User participating in tournaments
4. **Event Organizer/Admin** - Administrator managing events and system
5. **Shop Customer** - User purchasing items from the shop
6. **Game Server** - Automated system integration
7. **Spectator** - User viewing but not participating in events

---

## 1. Guest/Visitor User Stories

### Authentication & Registration
- 1.A.1 As a guest, I want to view available login methods, so that I can choose my preferred authentication method
- 1.A.2 As a guest, I want to register with email and password, so that I can create an account
- 1.A.3 As a guest, I want to register using Steam, so that I can quickly sign up with my gaming identity
- 1.A.4 As a guest, I want to see terms and conditions during registration, so that I understand the platform rules
- 1.A.5 As a guest, I want to reset my forgotten password, so that I can regain access to my account
- 1.A.6 As a guest, I want to verify my email address, so that I can activate my account
- 1.A.7 As a guest, I want to resend verification emails, so that I can complete registration if I didn't receive the first email

### Browsing & Discovery
- 1.B.1 As a guest, I want to view the homepage, so that I can see what the platform offers
- 1.B.2 As a guest, I want to browse all upcoming events, so that I can see what's available
- 1.B.3 As a guest, I want to view event details, so that I can learn about specific events
- 1.B.4 As a guest, I want to see event timetables, so that I can plan my attendance
- 1.B.5 As a guest, I want to view tournament information, so that I can see competitive opportunities
- 1.B.6 As a guest, I want to browse news articles, so that I can stay informed
- 1.B.7 As a guest, I want to view the gallery, so that I can see photos from past events
- 1.B.8 As a guest, I want to access the help/FAQ section, so that I can get answers to common questions
- 1.B.9 As a guest, I want to view public polls, so that I can see community opinions
- 1.B.10 As a guest, I want to see available game servers, so that I can check server status
- 1.B.11 As a guest, I want to view shop categories and items, so that I can browse merchandise

### Information Access
- 1.C.1 As a guest, I want to read legal notices, so that I understand the organization's legal standing
- 1.C.2 As a guest, I want to view terms and conditions, so that I know the rules
- 1.C.3 As a guest, I want to see the privacy policy, so that I understand how my data is handled
- 1.C.4 As a guest, I want to view contact information, so that I can reach out with questions
- 1.C.5 As a guest, I want to see about page, so that I can learn about the organization

---

## 2. Participant User Stories

### Account Management
- 2.A.1 As a participant, I want to log in to my account, so that I can access member features
- 2.A.2 As a participant, I want to log out, so that I can secure my account
- 2.A.3 As a participant, I want to view my profile, so that I can see my account information
- 2.A.4 As a participant, I want to update my profile details, so that I can keep my information current
- 2.A.5 As a participant, I want to change my email address, so that I can use my current email
- 2.A.6 As a participant, I want to upload a custom avatar, so that I can personalize my profile
- 2.A.7 As a participant, I want to select between local and third-party avatars, so that I can choose my preferred image
- 2.A.8 As a participant, I want to add additional login methods, so that I have multiple ways to access my account
- 2.A.9 As a participant, I want to remove login methods, so that I can manage my authentication options
- 2.A.10 As a participant, I want to delete my account, so that I can remove my data from the platform
- 2.A.11 As a participant, I want to generate API tokens, so that I can integrate with external applications
- 2.A.12 As a participant, I want to use a token wizard, so that I can easily create tokens for specific apps
- 2.A.13 As a participant, I want to revoke API tokens, so that I can secure my account

### Event Participation
- 2.B.1 As a participant, I want to browse all events, so that I can find events to attend
- 2.B.2 As a participant, I want to view detailed event information, so that I can decide if I want to attend
- 2.B.3 As a participant, I want to see event timetables, so that I can plan my day
- 2.B.4 As a participant, I want to view event attendees, so that I can see who else is coming
- 2.B.5 As a participant, I want to view event announcements, so that I stay informed about updates
- 2.B.6 As a participant, I want to generate ICS calendar files, so that I can add events to my calendar
- 2.B.7 As a participant, I want to view event-specific pages during the event, so that I can see real-time information

### Ticket Management
- 2.C.1 As a participant, I want to purchase event tickets, so that I can attend events
- 2.C.2 As a participant, I want to select ticket types, so that I can choose the right ticket for me
- 2.C.3 As a participant, I want to see my purchased tickets, so that I can manage my registrations
- 2.C.4 As a participant, I want to view my ticket QR code, so that I can check in at the event
- 2.C.5 As a participant, I want to export my ticket as a file, so that I can save it offline
- 2.C.6 As a participant, I want to update my ticket information, so that I can correct details
- 2.C.7 As a participant, I want to gift tickets to other users, so that I can invite friends
- 2.C.8 As a participant, I want to accept gifted tickets, so that I can receive tickets from others
- 2.C.9 As a participant, I want to revoke gifts I've sent, so that I can take back tickets if needed
- 2.C.10 As a participant, I want to retrieve tickets assigned to me, so that I can access staff or free tickets
- 2.C.11 As a participant, I want to view audit logs for my tickets, so that I can see the history of changes

### Seating
- 2.D.1 As a participant, I want to view available seating plans, so that I can see seating options
- 2.D.2 As a participant, I want to select my seat, so that I can choose where I sit
- 2.D.3 As a participant, I want to release my seat, so that I can free it for others
- 2.D.4 As a participant, I want to see who is sitting near me, so that I can identify neighbors

### Tournament Participation
- 2.E.1 As a participant, I want to view tournament listings, so that I can find competitions
- 2.E.2 As a participant, I want to register for 1v1 tournaments, so that I can compete individually
- 2.E.3 As a participant, I want to register my team for tournaments, so that we can compete together
- 2.E.4 As a participant, I want to register for PUG tournaments, so that I can join random teams
- 2.E.5 As a participant, I want to unregister from tournaments, so that I can withdraw if needed
- 2.E.6 As a participant, I want to view tournament brackets, so that I can see matchups
- 2.E.7 As a participant, I want to see tournament rules, so that I understand the competition format
- 2.E.8 As a participant, I want to view match schedules, so that I know when to play

### Shopping
- 2.F.1 As a participant, I want to browse shop items, so that I can see what's available
- 2.F.2 As a participant, I want to add items to my basket, so that I can purchase multiple items
- 2.F.3 As a participant, I want to update basket quantities, so that I can change order amounts
- 2.F.4 As a participant, I want to view my basket, so that I can review my order
- 2.F.5 As a participant, I want to checkout, so that I can complete my purchase
- 2.F.6 As a participant, I want to view my order history, so that I can track past purchases
- 2.F.7 As a participant, I want to view order details, so that I can see order status

### Payments
- 2.G.1 As a participant, I want to select payment methods, so that I can choose how to pay
- 2.G.2 As a participant, I want to use PayPal, so that I can pay securely online
- 2.G.3 As a participant, I want to use Stripe, so that I can pay with credit cards
- 2.G.4 As a participant, I want to use credits, so that I can spend my account balance
- 2.G.5 As a participant, I want to review my order before payment, so that I can verify details
- 2.G.6 As a participant, I want to see payment confirmation, so that I know my payment succeeded
- 2.G.7 As a participant, I want to see clear error messages if payment fails, so that I can retry

### Social Features
- 2.H.1 As a participant, I want to comment on news articles, so that I can engage with content
- 2.H.2 As a participant, I want to edit my comments, so that I can correct mistakes
- 2.H.3 As a participant, I want to delete my comments, so that I can remove unwanted posts
- 2.H.4 As a participant, I want to report inappropriate comments, so that moderators can review them
- 2.H.5 As a participant, I want to vote in polls, so that I can share my opinion
- 2.H.6 As a participant, I want to change my vote, so that I can update my choice
- 2.H.7 As a participant, I want to abstain from polls, so that I can withdraw my vote
- 2.H.8 As a participant, I want to suggest poll options, so that I can add choices

### Matchmaking
- 2.I.1 As a participant, I want to create matchmaking lobbies, so that I can organize casual matches
- 2.I.2 As a participant, I want to join matchmaking lobbies, so that I can play with others
- 2.I.3 As a participant, I want to invite users to matches, so that I can play with specific people
- 2.I.4 As a participant, I want to add teams to matches, so that I can organize team play
- 2.I.5 As a participant, I want to add players to teams, so that I can build rosters
- 2.I.6 As a participant, I want to remove players from teams, so that I can manage rosters
- 2.I.7 As a participant, I want to change player teams, so that I can balance teams
- 2.I.8 As a participant, I want to start matches, so that I can begin playing
- 2.I.9 As a participant, I want to scramble teams, so that I can randomize team composition
- 2.I.10 As a participant, I want to finalize matches, so that I can complete and save results
- 2.I.11 As a participant, I want to delete my matches, so that I can cancel games
- 2.I.12 As a participant, I want to view match details, so that I can see game information

---

## 3. Tournament Player User Stories

### Registration & Teams
- 3.A.1 As a tournament player, I want to register for tournaments quickly, so that I can secure my spot
- 3.A.2 As a tournament player, I want to create a team, so that I can compete with friends
- 3.A.3 As a tournament player, I want to invite players to my team, so that I can build a roster
- 3.A.4 As a tournament player, I want to manage my team roster, so that I can organize my team
- 3.A.5 As a tournament player, I want to leave a team, so that I can withdraw from team play
- 3.A.6 As a tournament player, I want to see my team members, so that I know who I'm playing with

### Competition
- 3.B.1 As a tournament player, I want to view my match schedule, so that I know when to play
- 3.B.2 As a tournament player, I want to see my opponents, so that I can prepare for matches
- 3.B.3 As a tournament player, I want to view tournament brackets, so that I can track progression
- 3.B.4 As a tournament player, I want to see live match updates, so that I can follow the tournament
- 3.B.5 As a tournament player, I want to view match results, so that I can see outcomes
- 3.B.6 As a tournament player, I want to see tournament standings, so that I can track my ranking
- 3.B.7 As a tournament player, I want to view tournament rules, so that I understand the format

### Server Integration
- 3.C.1 As a tournament player, I want to receive server connection details, so that I can join my match
- 3.C.2 As a tournament player, I want automatic server provisioning, so that I don't have to find servers
- 3.C.3 As a tournament player, I want match statistics recorded, so that my performance is tracked
- 3.C.4 As a tournament player, I want demo files saved, so that I can review matches

---

## 4. Event Organizer/Admin User Stories

### Event Management
- 4.A.1 As an admin, I want to create new events, so that I can organize gatherings
- 4.A.2 As an admin, I want to edit event details, so that I can update information
- 4.A.3 As an admin, I want to set event dates and times, so that participants know when to attend
- 4.A.4 As an admin, I want to set event capacity, so that I can limit attendees
- 4.A.5 As an admin, I want to upload event images, so that events look appealing
- 4.A.6 As an admin, I want to create event descriptions, so that participants know what to expect
- 4.A.7 As an admin, I want to archive old events, so that I can keep the event list clean
- 4.A.8 As an admin, I want to delete events, so that I can remove cancelled events
- 4.A.9 As an admin, I want to add event information sections, so that I can provide detailed content
- 4.A.10 As an admin, I want to update event information, so that I can keep content current
- 4.A.11 As an admin, I want to delete information sections, so that I can remove outdated content

### Ticket Type Management
- 4.B.1 As an admin, I want to create ticket types, so that I can offer different ticket options
- 4.B.2 As an admin, I want to set ticket prices, so that I can charge appropriately
- 4.B.3 As an admin, I want to set ticket quantities, so that I can control capacity
- 4.B.4 As an admin, I want to configure sale periods, so that I can control when tickets are available
- 4.B.5 As an admin, I want to mark tickets as seat-eligible, so that I can control seating
- 4.B.6 As an admin, I want to create ticket groups, so that I can organize related tickets
- 4.B.7 As an admin, I want to edit ticket types, so that I can update ticket details
- 4.B.8 As an admin, I want to delete ticket types, so that I can remove unwanted options

### Participant Management
- 4.C.1 As an admin, I want to view all participants, so that I can see who's attending
- 4.C.2 As an admin, I want to search for participants, so that I can find specific people
- 4.C.3 As an admin, I want to check in participants, so that I can track attendance
- 4.C.4 As an admin, I want to check out participants, so that I can track departures
- 4.C.5 As an admin, I want to bulk check out all participants, so that I can clear attendance at event end
- 4.C.6 As an admin, I want to scan QR codes for check-in, so that I can process arrivals quickly
- 4.C.7 As an admin, I want to view participant details, so that I can see their information
- 4.C.8 As an admin, I want to edit participant information, so that I can correct errors
- 4.C.9 As an admin, I want to transfer tickets between users, so that I can reassign tickets
- 4.C.10 As an admin, I want to revoke tickets, so that I can handle refunds or violations
- 4.C.11 As an admin, I want to delete tickets (super admin), so that I can remove erroneous entries

### Free Tickets & Gifts
- 4.D.1 As an admin, I want to assign staff tickets, so that I can give free access to team members
- 4.D.2 As an admin, I want to gift tickets to users, so that I can reward or invite people
- 4.D.3 As an admin, I want to track gifted tickets, so that I can see who received free access

### Seating Management
- 4.E.1 As an admin, I want to create seating plans, so that I can organize venue layout
- 4.E.2 As an admin, I want to edit seating plans, so that I can adjust layouts
- 4.E.3 As an admin, I want to add seats to plans, so that I can define capacity
- 4.E.4 As an admin, I want to remove seats, so that I can adjust layouts
- 4.E.5 As an admin, I want to manually assign seats, so that I can place specific people
- 4.E.6 As an admin, I want to remove seat assignments, so that I can free up seats
- 4.E.7 As an admin, I want to view seating occupancy, so that I can see availability

### Tournament Management
- 4.F.1 As an admin, I want to create tournaments, so that I can organize competitions
- 4.F.2 As an admin, I want to configure tournament formats, so that I can choose competition type
- 4.F.3 As an admin, I want to set tournament rules, so that participants know the format
- 4.F.4 As an admin, I want to set registration deadlines, so that I can control sign-ups
- 4.F.5 As an admin, I want to start tournaments, so that I can begin competition
- 4.F.6 As an admin, I want to finalize tournaments, so that I can lock results
- 4.F.7 As an admin, I want to manually add teams, so that I can include late registrations
- 4.F.8 As an admin, I want to update match results, so that I can correct scores
- 4.F.9 As an admin, I want to remove participants, so that I can handle withdrawals
- 4.F.10 As an admin, I want to assign participants to teams, so that I can organize PUGs
- 4.F.11 As an admin, I want to enable live bracket editing, so that I can make real-time changes
- 4.F.12 As an admin, I want to disable live editing, so that I can lock brackets
- 4.F.13 As an admin, I want to assign servers to matches, so that players have places to play
- 4.F.14 As an admin, I want to update match servers, so that I can change server assignments
- 4.F.15 As an admin, I want to delete match servers, so that I can remove assignments

### Timetable Management
- 4.G.1 As an admin, I want to create timetables, so that I can schedule event activities
- 4.G.2 As an admin, I want to add timetable entries, so that I can define schedule items
- 4.G.3 As an admin, I want to edit timetable entries, so that I can update schedules
- 4.G.4 As an admin, I want to delete timetable entries, so that I can remove cancelled activities
- 4.G.5 As an admin, I want to set entry times, so that participants know when things happen
- 4.G.6 As an admin, I want to delete timetables, so that I can remove old schedules

### Announcements
- 4.H.1 As an admin, I want to create announcements, so that I can communicate with attendees
- 4.H.2 As an admin, I want to update announcements, so that I can correct information
- 4.H.3 As an admin, I want to delete announcements, so that I can remove outdated messages

### Sponsors
- 4.I.1 As an admin, I want to add sponsors to events, so that I can display supporter logos
- 4.I.2 As an admin, I want to update sponsor information, so that I can keep sponsor data current
- 4.I.3 As an admin, I want to delete sponsors, so that I can remove past sponsors
- 4.I.4 As an admin, I want to order sponsors, so that I can prioritize display

### Venue Management
- 4.J.1 As an admin, I want to create venues, so that I can define event locations
- 4.J.2 As an admin, I want to edit venue details, so that I can update location information
- 4.J.3 As an admin, I want to upload venue images, so that participants can see the location
- 4.J.4 As an admin, I want to delete venues, so that I can remove unused locations

### Game & Server Management
- 4.K.1 As an admin, I want to add games, so that I can support different titles
- 4.K.2 As an admin, I want to edit game details, so that I can update game information
- 4.K.3 As an admin, I want to delete games, so that I can remove unsupported titles
- 4.K.4 As an admin, I want to deploy game templates, so that I can quickly set up common games
- 4.K.5 As an admin, I want to add game servers, so that I can provide server infrastructure
- 4.K.6 As an admin, I want to edit server details, so that I can update server information
- 4.K.7 As an admin, I want to monitor server status, so that I can ensure servers are online
- 4.K.8 As an admin, I want to delete game servers, so that I can remove decommissioned servers
- 4.K.9 As an admin, I want to update server tokens, so that I can maintain API security
- 4.K.10 As an admin, I want to create server commands, so that I can define remote operations
- 4.K.11 As an admin, I want to create command parameters, so that I can customize commands
- 4.K.12 As an admin, I want to execute commands on servers, so that I can manage servers remotely
- 4.K.13 As an admin, I want to execute tournament commands, so that I can automate match setup
- 4.K.14 As an admin, I want to execute matchmaking commands, so that I can provision casual matches
- 4.K.15 As an admin, I want to delete match replays, so that I can manage storage

### Matchmaking Administration
- 4.L.1 As an admin, I want to view all matchmaking lobbies, so that I can monitor casual play
- 4.L.2 As an admin, I want to create matchmaking lobbies, so that I can organize play
- 4.L.3 As an admin, I want to manage matchmaking teams, so that I can organize players
- 4.L.4 As an admin, I want to start matches, so that I can initiate games
- 4.L.5 As an admin, I want to finalize matches, so that I can record results
- 4.L.6 As an admin, I want to delete matches, so that I can clean up old games
- 4.L.7 As an admin, I want to assign servers to matches, so that players have game servers

### User Management
- 4.M.1 As an admin, I want to view all users, so that I can see the user base
- 4.M.2 As an admin, I want to search for users, so that I can find specific accounts
- 4.M.3 As an admin, I want to view user details, so that I can see account information
- 4.M.4 As an admin, I want to grant admin privileges, so that I can add administrators
- 4.M.5 As an admin, I want to remove admin privileges, so that I can revoke admin access
- 4.M.6 As an admin, I want to ban users, so that I can enforce rules
- 4.M.7 As an admin, I want to unban users, so that I can restore access
- 4.M.8 As an admin, I want to verify user emails manually, so that I can help users with issues
- 4.M.9 As an admin, I want to remove email verification, so that I can reset verification status
- 4.M.10 As an admin, I want to unauthorize third-party logins, so that I can remove compromised connections
- 4.M.11 As an admin, I want to delete users (super admin), so that I can remove accounts

### News Management
- 4.N.1 As an admin, I want to create news articles, so that I can share information
- 4.N.2 As an admin, I want to edit news articles, so that I can update content
- 4.N.3 As an admin, I want to delete news articles, so that I can remove old content
- 4.N.4 As an admin, I want to moderate comments, so that I can maintain community standards
- 4.N.5 As an admin, I want to approve comments, so that I can allow appropriate content
- 4.N.6 As an admin, I want to reject comments, so that I can block inappropriate content
- 4.N.7 As an admin, I want to delete comments, so that I can remove violations
- 4.N.8 As an admin, I want to review comment reports, so that I can address issues
- 4.N.9 As an admin, I want to delete comment reports, so that I can manage the report queue

### Poll Management
- 4.O.1 As an admin, I want to create polls, so that I can gather community opinions
- 4.O.2 As an admin, I want to edit polls, so that I can update questions
- 4.O.3 As an admin, I want to add poll options, so that users have choices
- 4.O.4 As an admin, I want to delete poll options, so that I can remove inappropriate choices
- 4.O.5 As an admin, I want to end polls, so that I can close voting
- 4.O.6 As an admin, I want to delete polls, so that I can remove old polls

### Gallery Management
- 4.P.1 As an admin, I want to create albums, so that I can organize photos
- 4.P.2 As an admin, I want to edit albums, so that I can update album details
- 4.P.3 As an admin, I want to delete albums, so that I can remove old galleries
- 4.P.4 As an admin, I want to upload images, so that I can add photos
- 4.P.5 As an admin, I want to edit image details, so that I can add descriptions
- 4.P.6 As an admin, I want to delete images, so that I can remove unwanted photos

### Help System Management
- 4.Q.1 As an admin, I want to create help categories, so that I can organize support content
- 4.Q.2 As an admin, I want to edit help categories, so that I can update organization
- 4.Q.3 As an admin, I want to delete help categories, so that I can remove unused sections
- 4.Q.4 As an admin, I want to add help entries, so that I can provide documentation
- 4.Q.5 As an admin, I want to edit help entries, so that I can update documentation
- 4.Q.6 As an admin, I want to delete help entries, so that I can remove outdated help
- 4.Q.7 As an admin, I want to upload attachments, so that I can provide supplementary files
- 4.Q.8 As an admin, I want to delete attachments, so that I can remove old files

### Shop Management
- 4.R.1 As an admin, I want to create shop categories, so that I can organize products
- 4.R.2 As an admin, I want to edit categories, so that I can update organization
- 4.R.3 As an admin, I want to delete categories, so that I can remove unused sections
- 4.R.4 As an admin, I want to add shop items, so that I can sell products
- 4.R.5 As an admin, I want to edit items, so that I can update product details
- 4.R.6 As an admin, I want to delete items, so that I can remove discontinued products
- 4.R.7 As an admin, I want to upload item images, so that products look appealing
- 4.R.8 As an admin, I want to manage item inventory, so that I can track stock

### Order Management
- 4.S.1 As an admin, I want to view all orders, so that I can see sales
- 4.S.2 As an admin, I want to view order details, so that I can see what was ordered
- 4.S.3 As an admin, I want to mark orders as processing, so that I can track fulfillment
- 4.S.4 As an admin, I want to mark orders as shipped, so that customers know items are on the way
- 4.S.5 As an admin, I want to add tracking information, so that customers can track shipments
- 4.S.6 As an admin, I want to mark orders as complete, so that I can close fulfilled orders
- 4.S.7 As an admin, I want to cancel orders, so that I can handle cancellations

### Purchase Management
- 4.T.1 As an admin, I want to view all purchases, so that I can see financial activity
- 4.T.2 As an admin, I want to filter shop purchases, so that I can see merchandise sales
- 4.T.3 As an admin, I want to filter event purchases, so that I can see ticket sales
- 4.T.4 As an admin, I want to view revoked purchases, so that I can see refunds
- 4.T.5 As an admin, I want to manually mark purchases as successful, so that I can handle payment issues
- 4.T.6 As an admin, I want to delete purchases (super admin), so that I can remove erroneous entries
- 4.T.7 As an admin, I want to inspect one purchase, so that i can confirm items bought and transaction details.

### Credit System Management
- 4.U.1 As an admin, I want to enable the credit system, so that users can use credits
- 4.U.2 As an admin, I want to disable the credit system, so that I can turn off credits
- 4.U.3 As an admin, I want to add credits to users, so that I can reward or compensate
- 4.U.4 As an admin, I want to remove credits from users, so that I can handle errors
- 4.U.5 As an admin, I want to configure credit settings, so that I can control how credits work
- 4.U.6 As an admin, I want to view credit transactions, so that I can audit credit use

### Mailing System
- 4.V.1 As an admin, I want to create email templates, so that I can send bulk emails
- 4.V.2 As an admin, I want to edit email templates, so that I can update messages
- 4.V.3 As an admin, I want to send emails to all users, so that I can make announcements
- 4.V.4 As an admin, I want to send emails to event participants, so that I can communicate about events
- 4.V.5 As an admin, I want to view email history, so that I can see what was sent
- 4.V.6 As an admin, I want to delete email templates, so that I can remove unused templates

### System Settings
- 4.W.1 As an admin, I want to configure organization details, so that branding is correct
- 4.W.2 As an admin, I want to configure authentication settings, so that I can control login options
- 4.W.3 As an admin, I want to enable/disable login methods, so that I can control auth options
- 4.W.4 As an admin, I want to configure payment gateways, so that I can accept payments
- 4.W.5 As an admin, I want to enable/disable payment methods, so that I can control payment options
- 4.W.6 As an admin, I want to configure API settings, so that I can control API access
- 4.W.7 As an admin, I want to enable/disable system features, so that I can control what's available
- 4.W.8 As an admin, I want to configure Steam API, so that Steam login works
- 4.W.9 As an admin, I want to regenerate QR codes, so that I can update ticket codes

### Appearance Settings
- 4.X.1 As an admin, I want to upload slider images, so that I can customize the homepage
- 4.X.2 As an admin, I want to edit CSS variables, so that I can customize colors and styling
- 4.X.3 As an admin, I want to override CSS, so that I can make custom style changes
- 4.X.4 As an admin, I want to recompile CSS, so that changes take effect
- 4.X.5 As an admin, I want to update CSS from files, so that I can sync with development

---

## 5. Shop Customer User Stories

### Shopping
- 5.A.1 As a shop customer, I want to browse categories, so that I can find products
- 5.A.2 As a shop customer, I want to view item details, so that I can learn about products
- 5.A.3 As a shop customer, I want to see product images, so that I know what I'm buying
- 5.A.4 As a shop customer, I want to add items to cart, so that I can buy multiple items
- 5.A.5 As a shop customer, I want to update quantities, so that I can buy the right amount
- 5.A.6 As a shop customer, I want to remove items, so that I can change my order

### Ordering
- 5.B.1 As a shop customer, I want to checkout, so that I can complete my purchase
- 5.B.2 As a shop customer, I want to view order confirmation, so that I know my order was received
- 5.B.3 As a shop customer, I want to track my order, so that I know when it will arrive
- 5.B.4 As a shop customer, I want to view order history, so that I can see past purchases
- 5.B.5 As a shop customer, I want to see order status, so that I know fulfillment progress

---

## 6. Game Server User Stories

### API Integration
- 6.A.1 As a game server, I want to authenticate with API tokens, so that I can access protected endpoints
- 6.A.2 As a game server, I want to retrieve match configurations, so that I can set up matches
- 6.A.3 As a game server, I want to upload demo files, so that matches can be reviewed
- 6.A.4 As a game server, I want to notify when server is free, so that it can be reassigned
- 6.A.5 As a game server, I want to finalize matches, so that results are recorded
- 6.A.6 As a game server, I want to finalize maps, so that individual map results are saved
- 6.A.7 As a game server, I want to send go-live notifications, so that match start is recorded
- 6.A.8 As a game server, I want to update round statistics, so that performance is tracked
- 6.A.9 As a game server, I want to update player statistics, so that individual performance is tracked

---

## 7. Spectator User Stories

### Viewing Events
- 7.A.1 As a spectator, I want to purchase spectator tickets, so that I can attend without playing
- 7.A.2 As a spectator, I want to view event timetables, so that I know the schedule
- 7.A.3 As a spectator, I want to see tournament brackets, so that I can follow competitions
- 7.A.4 As a spectator, I want to view live match updates, so that I can track ongoing games
- 7.A.5 As a spectator, I want to see tournament standings, so that I know who's winning
- 7.A.6 As a spectator, I want to view the big screen display, so that I can see event info on venue screens
- 7.A.7 As a spectator, I want to view announcements, so that I stay informed
- 7.A.8 As a spectator, I want to browse event photos, so that I can see event highlights

---

## Summary

This comprehensive list contains **300+ user stories** covering all major personas and interactions within the Eventula event management system. These stories can be used for:

- **Sprint planning** - Select stories for implementation cycles
- **Feature prioritization** - Rank stories by business value
- **Testing** - Use as acceptance criteria
- **Documentation** - Reference for feature explanations
- **Stakeholder communication** - Share what the system can do

Each story has been numbered using the schema `<Nr>.<Letter A-Z>.<Nr>` where:
- First number represents the user persona (1-7)
- Letter represents the feature category within that persona
- Final number represents the specific story within that category