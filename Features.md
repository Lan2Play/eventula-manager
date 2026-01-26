# Eventula Manager - Feature List

## Overview
Eventula is a comprehensive white-labeled event management system designed for LAN parties, gaming events, and esports tournaments. The system provides end-to-end management capabilities from event creation to participant check-in and tournament execution.

---

## 1. Core System Features

### 1.1 White Label Capabilities
- Fully customizable branding and appearance
- Custom logo and color schemes
- CSS variable editor for theme customization
- Web-based CSS editor
- Custom domain support
- Light and dark theme out of the box
- Bootstrap-based expandable templating system

### 1.2 Multi-language Support
- Currently supports English (EN) and German (DE)
- User-selectable locale preferences
- Admin controls for user locale management
- Extensible translation system via Weblate

### 1.3 Docker Infrastructure
- Fully encapsulated in Docker containers
- NGINX, MySQL & PHP Docker stack
- Self-contained deployment
- Easy scalability
- Development and production configurations

### 1.4 Technology Stack
- Built on Laravel framework
- RESTful API architecture
- Easily expandable codebase
- Modern PHP best practices
- Bootstrap frontend framework

---

## 2. Authentication & User Management

### 2.1 Multiple Login Gateways
- Standard email/password authentication
- Steam authentication
- Social login integration support
- SSO (Single Sign-On) capabilities
- Multiple authentication methods per user
- Admin-controlled login method enable/disable

### 2.2 User Account Management
- User profile management
- Avatar selection (local upload or third-party)
- Email verification system
- Password reset functionality
- Phone number optional verification
- Account deletion capabilities
- Multi-factor authentication support

### 2.3 User Registration
- Multiple registration methods
- Email verification workflow
- Terms and conditions acceptance
- Custom registration fields
- Account linking for multiple auth methods

### 2.4 User Administration
- User search and management
- Admin role assignment
- User banning/unbanning
- Email verification management
- Third-party authorization removal
- User activity audit trails
- User deletion (super admin only)

### 2.5 API Token Management
- Personal access token generation
- Token wizard for application integration
- Sanctum-based API authentication
- Token revocation
- Application callback URL support

---

## 3. Event Management

### 3.1 Event Creation & Configuration
- Create unlimited events
- Online and in-person event types
- Event start and end dates
- Event capacity management
- Event description and details
- Event images and branding
- Event-specific settings
- Public/private event visibility
- Event archiving

### 3.2 Event Information Pages
- Custom event signup pages
- Event information display
- Event-specific announcements
- Event timetable display
- Attendee list display
- Tournament listings
- Seating plan visualization
- Event big screen mode

### 3.3 Event Sponsors
- Multiple sponsors per event
- Sponsor logo display
- Sponsor link management
- Sponsor ordering/priority

### 3.4 Event Analytics
- Participant statistics
- Ticket sales tracking
- Revenue reporting
- Attendance tracking
- Sign-in/sign-out logs

### 3.5 ICS Calendar Integration
- Generate ICS calendar files
- Calendar import for participants
- Event scheduling integration

---

## 4. Ticketing System

### 4.1 Ticket Types
- Multiple ticket types per event
- Weekend tickets
- Day tickets
- Spectator tickets
- Staff tickets
- Gift tickets
- Seat-eligible vs non-seat-eligible tickets
- Limited quantity tickets
- Unlimited quantity tickets

### 4.2 Ticket Groups
- Group multiple ticket types
- Ticket group management
- Group-based pricing strategies

### 4.3 Sale Periods
- Configurable sale start dates
- Configurable sale end dates
- Early bird pricing support
- Sale period restrictions

### 4.4 Ticket Purchasing
- Shopping cart functionality
- Multiple ticket purchase
- Ticket type selection
- Price display with currency
- Payment gateway integration

### 4.5 Ticket Management
- QR code generation
- QR code-based check-in
- Ticket transfer between users
- Ticket gifting system
- Gift acceptance/rejection
- Gift revocation
- Ticket refunds
- Ticket revocation
- Staff ticket assignment
- Participant ticket editing

### 4.6 Check-in System
- QR code scanning
- Manual check-in
- Check-out functionality
- Bulk check-out all participants
- Real-time attendance tracking
- Sign-in timestamps
- API-based check-in

### 4.7 Participant Management
- Participant list viewing
- Participant details management
- Custom participant fields
- Participant search and filtering
- Export participant data
- Participant file exports (various formats)
- Transfer tickets to other users
- Reset participant manager
- Reset participant user

---

## 5. Seating Management

### 5.1 Seating Plans
- Multiple seating plans per event
- Visual seating plan editor
- Custom seating layouts
- Row and column configuration
- Seat numbering systems
- Seat capacity management

### 5.2 Seat Assignment
- Manual seat assignment by admin
- Self-service seat selection by participants
- Seat reservation system
- Seat release functionality
- Seat eligibility based on ticket type
- Real-time seat availability

### 5.3 Seating Visualization
- Graphical seating plan display
- Color-coded seat status
- Seat occupant information
- Interactive seating maps
- Big screen seating display

---

## 6. Tournament Management

### 6.1 Tournament Creation
- Unlimited tournaments per event
- Tournament name and description
- Game selection
- Tournament format configuration
- Entry requirements
- Registration deadlines
- Team size configuration
- Participant limits

### 6.2 Tournament Formats
- Single elimination
- Double elimination
- Round robin
- 1v1 tournaments
- Team tournaments
- PUG (Pick-Up Group) tournaments

### 6.3 Challonge Integration
- Challonge API integration
- Automatic bracket generation
- Match scheduling
- Score reporting
- Bracket visualization
- Live bracket updates
- Live editing enable/disable

### 6.4 Tournament Registration
- Player self-registration
- Team registration
- PUG registration
- Registration withdrawal
- Team management
- Participant team assignment

### 6.5 Tournament Administration
- Start tournaments
- Finalize tournaments
- Add teams manually
- Update match results
- Update participant teams
- Remove participants
- Add single players
- Add PUG players
- Match server assignment

### 6.6 Tournament Rules
- Custom rules per tournament
- Rule display for participants
- Tournament-specific settings

### 6.7 Tournament Display
- Tournament brackets view
- Match schedule display
- Current standings
- Match results history
- Participant lists

---

## 7. Game & Game Server Management

### 7.1 Game Management
- Add/edit/delete games
- Game information and metadata
- Game images
- Associate tournaments with games
- Associate servers with games

### 7.2 Game Server Management
- Game server registration
- Server connection details
- Server status monitoring
- Public game server listing
- Server token management
- RCON support for remote management
- Server status gathering

### 7.3 Supported Game Protocols
- Maniaplanet/Trackmania RCON
- Source engine RCON
- GoldSource engine RCON
- Get5 integration for CS:GO
- Automated match server provisioning

### 7.4 Game Server Commands
- Custom server command creation
- Command parameter configuration
- Execute commands on servers
- Tournament match commands
- Matchmaking commands
- Command templates

### 7.5 Game Templates
- Pre-configured game templates
- Template deployment
- Quick game setup

### 7.6 Match Replays
- Demo file storage
- Demo file management
- Replay deletion
- Replay download

---

## 8. Matchmaking System

### 8.1 Match Creation
- Create custom matches
- Define match parameters
- Game selection
- Server assignment
- Team configuration

### 8.2 Team Management
- Add/remove teams
- Add/remove players to teams
- Change player teams
- Team scrambling
- Update team details

### 8.3 Match Lifecycle
- Open matches for joining
- Start matches
- Match in progress tracking
- Finalize matches
- Match history
- Delete matches

### 8.4 Match Server Integration
- Automatic server assignment
- Server configuration for matches
- Map selection
- Number of maps configuration
- Match go-live notifications
- Round updates
- Player statistics tracking

### 8.5 Match Invitations
- Invite system for matches
- Join via invitation
- Match participant management

### 8.6 Admin Match Control
- Full match administration
- Override match settings
- Force start/stop matches
- Server assignment management

---

## 9. Shop System

### 9.1 Shop Categories
- Create shop categories
- Category images
- Category descriptions
- Category ordering
- Enable/disable categories

### 9.2 Shop Items
- Add/edit/delete items
- Item name and description
- Item pricing
- Multiple item images
- Image management
- Item stock/inventory
- Item availability settings
- Item tags/categories

### 9.3 Shopping Experience
- Browse shop categories
- View item details
- Shopping basket/cart
- Update basket quantities
- Remove items from basket
- Checkout process

### 9.4 Order Management
- Order creation from purchases
- Order status tracking
- View all orders (admin)
- View personal orders (user)
- Order details display
- Order history

### 9.5 Order Processing (Admin)
- Set order as processing
- Set order as shipped
- Add tracking information
- Set order as complete
- Cancel orders
- Order fulfillment workflow

### 9.6 Shop System Control
- Enable/disable entire shop system
- Shop system settings
- Inventory management

---

## 10. Credit System

### 10.1 Credit Management
- User credit balance tracking
- Credit allocation to users
- Credit spending
- Credit refunds
- Credit transaction history

### 10.2 Credit Administration
- Add/remove credits from users
- Credit system settings
- Credit transaction logs
- Credit reporting

### 10.3 Credit Usage
- Use credits for ticket purchases
- Use credits for shop purchases
- Combined payment methods (credits + other)

### 10.4 System Control
- Enable/disable credit system
- Configure credit settings
- Credit-to-currency conversion

---

## 11. Payment Processing

### 11.1 Payment Gateways
- PayPal Express integration
- Stripe card payment integration
- Free/zero-cost provider
- Multiple gateway support
- Enable/disable gateways per admin settings

### 11.2 Payment Flow
- Shopping cart checkout
- Payment method selection
- Payment details entry
- Payment review page
- Delivery information
- Payment callback handling
- Payment POST processing

### 11.3 Payment Status
- Successful payment confirmation
- Failed payment handling
- Cancelled payment handling
- Pending payment tracking
- Payment retries

### 11.4 Terms & Conditions
- T&C display during checkout
- T&C acceptance requirement
- Custom T&C content management

### 11.5 Purchase Management
- Purchase history
- Purchase details
- Purchase status tracking
- Refund processing
- Revoked purchase management
- Manual purchase success marking
- Purchase deletion (super admin only)

### 11.6 Payment Reporting
- Revenue breakdowns
- Payment gateway reports
- Transaction history
- Financial analytics
- Shop vs event revenue separation

---

## 12. Timetable Management

### 12.1 Timetable Creation
- Multiple timetables per event
- Timetable naming
- Timetable descriptions
- Timetable status

### 12.2 Timetable Entries
- Add/edit/delete timetable entries
- Entry name and description
- Start and end times
- Entry location
- Entry type/category

### 12.3 Timetable Display
- View event timetables
- Chronological entry listing
- Current/upcoming entry highlighting
- Big screen timetable display
- API access to timetables

---

## 13. Announcement System

### 13.1 Event Announcements
- Create announcements per event
- Announcement content
- Announcement timestamps
- Update announcements
- Delete announcements

### 13.2 Announcement Display
- Display on event pages
- Display on big screen
- Real-time announcement updates
- Announcement history

---

## 14. News & Content Management

### 14.1 News Articles
- Create/edit/delete news articles
- Article title and content
- Article author attribution
- Publication timestamps
- Article images
- Rich text editing

### 14.2 News Tags
- Tag articles with categories
- Browse articles by tag
- Tag management
- Multi-tag support

### 14.3 News Comments
- User comments on articles
- Comment moderation
- Comment approval/rejection
- Comment editing
- Comment deletion
- Comment reporting system

### 14.4 Comment Moderation
- Review reported comments
- Approve/reject comments
- Delete comments
- Manage comment reports
- Ban users for comment violations

---

## 15. Polls & Voting

### 15.1 Poll Creation
- Create polls
- Poll questions
- Poll descriptions
- Poll duration/deadlines
- Enable/disable polls

### 15.2 Poll Options
- Add multiple options
- User-submitted options (optional)
- Option descriptions
- Delete options

### 15.3 Voting
- Cast votes
- Change votes
- Abstain from voting
- View results
- Anonymous vs public voting

### 15.4 Poll Results
- Real-time result display
- Vote counts
- Percentage breakdowns
- Result visualization
- End/close polls

---

## 16. Gallery System

### 16.1 Album Management
- Create photo albums
- Album names and descriptions
- Album cover images
- Enable/disable albums
- Delete albums

### 16.2 Image Management
- Upload images to albums
- Multiple image upload
- Image descriptions
- Image ordering
- Update image details
- Delete images

### 16.3 Gallery Display
- Browse all albums
- View album contents
- Image viewer/lightbox
- Image thumbnails
- Public gallery access

### 16.4 System Control
- Enable/disable gallery system
- Gallery settings

---

## 17. Help & FAQ System

### 17.1 Help Categories
- Create help categories
- Category names and descriptions
- Category ordering
- Enable/disable categories

### 17.2 Help Entries
- Add help articles/entries
- Entry content with rich text
- Entry titles
- Entry ordering within categories
- Update entries
- Delete entries

### 17.3 Help Attachments
- Upload files to help entries
- Attachment management
- Update attachment details
- Delete attachments

### 17.4 Help Display
- Browse help categories
- Search help content
- User-friendly help interface
- FAQ display

### 17.5 System Control
- Enable/disable help system
- Help system settings

---

## 18. Mailing & Newsletter System

### 18.1 Mail Templates
- Create email templates
- Template subject and body
- HTML email support
- Template variables/placeholders
- Save templates for reuse

### 18.2 Email Sending
- Send to all users
- Send to event participants
- Send to specific user groups
- Bulk email sending
- Email delivery tracking

### 18.3 Email Management
- View sent emails
- Email history
- Template management
- Delete templates

---

## 19. Venue Management

### 19.1 Venue Creation
- Add venues
- Venue name and description
- Venue address
- Venue capacity
- Venue contact information

### 19.2 Venue Images
- Upload venue images
- Multiple images per venue
- Update image details
- Delete images
- Image ordering

### 19.3 Venue Assignment
- Assign venues to events
- Venue details on event pages
- Multi-venue support

---

## 20. API & Integrations

### 20.1 Public API Endpoints
- Events API
- Upcoming events API
- Event details API
- Participants/tickets API
- Timetables API
- Ticket types API
- Seating API

### 20.2 Authenticated API
- User profile API (/api/user/me)
- User tickets API
- Personal data access

### 20.3 Game Server API
- Match configuration API
- Demo upload API
- Server free notification API
- Match finalization API
- Map finalization API
- Go-live notifications API
- Round update API
- Player statistics API
- Tournament match API
- Matchmaking match API

### 20.4 Admin API
- Participant sign-in API
- Participant details API
- Purchase management API
- Administrative operations via API

### 20.5 Webhooks & Callbacks
- Payment gateway webhooks
- External service integrations
- Event-driven notifications

### 20.6 Third-party Integrations
- Challonge API integration
- Get5 CS:GO integration
- Steam API integration
- Payment gateway APIs

---

## 21. Administration & Settings

### 21.1 Dashboard
- Admin overview dashboard
- Key statistics
- Recent activity
- Quick actions

### 21.2 General Settings
- Site name and branding
- Organization details
- Contact information
- Social media links
- SEO settings

### 21.3 System Settings
- Enable/disable major features
- System-wide configurations
- Debug mode controls
- Maintenance mode

### 21.4 Authentication Settings
- Configure login methods
- Enable/disable auth providers
- Steam API configuration
- OAuth settings
- Email verification requirements
- Phone number requirements

### 21.5 Payment Settings
- Configure payment gateways
- Gateway credentials
- Enable/disable payment methods
- Currency settings
- Tax configuration

### 21.6 API Settings
- API key management
- Rate limiting
- CORS configuration
- API documentation access

### 21.7 Feature Toggles
- Enable/disable credit system
- Enable/disable shop
- Enable/disable gallery
- Enable/disable help system
- Enable/disable matchmaking
- System-wide feature control

### 21.8 Appearance Settings
- Upload homepage slider images
- Slider image management
- CSS compilation
- CSS override editor
- CSS variable editor
- Theme customization

### 21.9 QR Code Management
- Regenerate all QR codes
- Generate QR codes with new names
- QR code settings

### 21.10 User Locale Management
- Enable/disable user locale selection
- Reset user locales
- Default locale settings

---

## 22. Security & Compliance

### 22.1 Data Protection
- GDPR compliance features
- Data protection page
- Privacy policy management
- User data export
- User data deletion
- Cookie consent management

### 22.2 Legal Pages
- Terms and conditions
- Legal notice/imprint
- Privacy policy
- Custom legal pages

### 22.3 EU Cookie Consent
- Cookie consent banner
- Consent tracking
- Cookie policy display

### 22.4 User Privacy
- Account deletion
- Data anonymization
- Consent management

### 22.5 Security Features
- Email verification
- Password reset security
- Banned user prevention
- Admin-only areas
- Super danger zone (additional protection)

---

## 23. Audit & Logging

### 23.1 Audit Trails
- Ticket audit logs
- User action tracking
- Administrative action logs
- Change history

### 23.2 Activity Monitoring
- User login tracking
- Purchase tracking
- Modification logs

---

## 24. Search Functionality

### 24.1 User Search
- User autocomplete
- Search by username
- Search by email
- Quick user lookup

### 24.2 Content Search
- Search across events
- Search across news
- Search across help articles

---

## 25. File & Media Management

### 25.1 Image Handling
- Image uploads
- WebP conversion
- Image optimization
- Thumbnail generation
- Multiple image formats support

### 25.2 File Storage
- Demo/replay file storage
- Document attachments
- Gallery image storage
- Avatar storage
- Venue image storage

---

## 26. Accessibility & UX

### 26.1 Responsive Design
- Mobile-friendly interface
- Tablet optimization
- Desktop layouts
- Responsive navigation

### 26.2 User Experience
- Intuitive navigation
- Clear call-to-actions
- Progress indicators
- Error messaging
- Success confirmations
- Loading states

### 26.3 Big Screen Mode
- Event-specific big screen view
- Display timetables
- Display attendees
- Display tournaments
- Display announcements
- Display seating plans
- Ideal for venue displays

---

## 27. Advanced Features

### 27.1 Custom Fields
- Extensible data models
- Custom participant fields
- Custom event fields

### 27.2 Installation System
- Guided installation wizard
- Initial setup configuration
- Database setup
- Admin account creation

### 27.3 PHPInfo (Debug Mode)
- PHP configuration viewing
- Debug information access
- System diagnostics

---

## 28. Future Expansion

### 28.1 Extensibility
- Plugin architecture support
- Custom module development
- API-first design
- Webhook system
- Template override system

### 28.2 Integration Points
- Third-party service integration
- Custom authentication providers
- Custom payment gateways
- External tournament systems

---

## Summary

Eventula Manager provides a complete ecosystem for managing gaming events, LAN parties, and esports tournaments. With over 250+ distinct features across 28 major categories, it offers everything needed to run successful events from initial planning through execution and post-event analysis. The system is designed to be white-labeled, extensible, and scalable to events of any size.
