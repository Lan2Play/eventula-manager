<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereValue($value)
 */
	class ApiKey extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appearance whereValue($value)
 */
	class Appearance extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property int $amount
 * @property string $reason
 * @property int|null $purchase_id
 * @property int|null $admin_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User|null $admin
 * @property-read \App\Purchase|null $purchase
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditLog whereUserId($value)
 */
	class CreditLog extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $display_name
 * @property string $nice_name
 * @property string $slug
 * @property string $status
 * @property int $capacity
 * @property int|null $event_venue_id
 * @property string $start
 * @property string $end
 * @property string|null $desc_long
 * @property string|null $desc_short
 * @property string|null $essential_info
 * @property string|null $event_live_info
 * @property int $online_event
 * @property int $private_participants
 * @property int $matchmaking_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventParticipant> $allEventParticipants
 * @property-read int|null $all_event_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventAnnouncement> $announcements
 * @property-read int|null $announcements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventParticipant> $eventParticipants
 * @property-read int|null $event_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GalleryAlbum> $galleries
 * @property-read int|null $galleries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventInformation> $information
 * @property-read int|null $information_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Poll> $polls
 * @property-read int|null $polls_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventSeatingPlan> $seatingPlans
 * @property-read int|null $seating_plans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventSponsor> $sponsors
 * @property-read int|null $sponsors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTicket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTimetable> $timetables
 * @property-read int|null $timetables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournament> $tournaments
 * @property-read int|null $tournaments_count
 * @property-read \App\EventVenue|null $venue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEssentialInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventLiveInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventVenueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereMatchmakingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereNiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereOnlineEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePrivateParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Event extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventAnnouncement whereUpdatedAt($value)
 */
	class EventAnnouncement extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_id
 * @property string $title
 * @property string|null $text
 * @property string|null $image_path
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventInformation whereUpdatedAt($value)
 */
	class EventInformation extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int|null $ticket_id
 * @property int|null $purchase_id
 * @property int $revoked
 * @property string|null $qrcode
 * @property int $staff
 * @property int $free
 * @property int|null $staff_free_assigned_by
 * @property int $signed_in
 * @property int $credit_applied
 * @property string|null $gift
 * @property string|null $gift_accepted
 * @property string|null $gift_accepted_url
 * @property string|null $gift_sendee
 * @property int $transferred
 * @property int $transferred_event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @property-read \App\Purchase|null $purchase
 * @property-read \App\EventSeating|null $seat
 * @property-read \App\EventTicket|null $ticket
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournamentParticipant> $tournamentParticipants
 * @property-read int|null $tournament_participants_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereCreditApplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereGiftAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereGiftAcceptedUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereGiftSendee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereQrcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereSignedIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereStaffFreeAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereTransferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereTransferredEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventParticipant whereUserId($value)
 */
	class EventParticipant extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int|null $column
 * @property int|null $row
 * @property string $status
 * @property int $event_seating_plan_id
 * @property int|null $event_participant_id
 * @property int $gifted
 * @property int|null $gifted_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\EventParticipant|null $eventParticipant
 * @property-read \App\EventSeatingPlan $seatingPlan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereEventParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereEventSeatingPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereGifted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereGiftedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereRow($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeating whereUpdatedAt($value)
 */
	class EventSeating extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $name_short
 * @property string $slug
 * @property int $event_id
 * @property int $columns
 * @property int $rows
 * @property string $headers
 * @property int $locked
 * @property string|null $image_path
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventSeating> $seats
 * @property-read int|null $seats_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereColumns($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereNameShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSeatingPlan withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class EventSeatingPlan extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property string $image_path
 * @property string|null $website
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSponsor whereWebsite($value)
 */
	class EventSponsor extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $tag_id
 * @property int $event_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTag whereUpdatedAt($value)
 */
	class EventTag extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $event_id
 * @property string $type
 * @property float $price
 * @property int|null $no_tickets_per_user
 * @property int|null $price_credit
 * @property int $seatable
 * @property int $quantity
 * @property string|null $sale_start
 * @property string|null $sale_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\CreditLog|null $creditLogs
 * @property-read \App\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventParticipant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereNoTicketsPerUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket wherePriceCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereSaleEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereSaleStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereSeatable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereUpdatedAt($value)
 */
	class EventTicket extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $event_id
 * @property string $status
 * @property int $primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTimetableData> $data
 * @property-read int|null $data_count
 * @property-read \App\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetable withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class EventTimetable extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_timetable_id
 * @property string|null $name
 * @property string $start_time
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\EventTimetable|null $timetable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereEventTimetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTimetableData whereUpdatedAt($value)
 */
	class EventTimetableData extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_id
 * @property string $challonge_tournament_id
 * @property string $challonge_tournament_url
 * @property string $name
 * @property string $slug
 * @property int|null $game_id
 * @property string|null $format
 * @property string|null $team_size
 * @property string $description
 * @property string|null $rules
 * @property int $allow_bronze
 * @property int $allow_player_teams
 * @property string $status
 * @property int $enable_live_editing
 * @property int $api_complete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $random_teams
 * @property int $match_autostart
 * @property int $match_autoapi
 * @property string $bestof
 * @property string $grand_finals_modifier
 * @property-read \App\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournamentMatchServer> $eventTournamentMatchServer
 * @property-read int|null $event_tournament_match_server_count
 * @property-read \App\Game|null $game
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournamentParticipant> $tournamentParticipants
 * @property-read int|null $tournament_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournamentTeam> $tournamentTeams
 * @property-read int|null $tournament_teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereAllowBronze($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereAllowPlayerTeams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereApiComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereBestof($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereChallongeTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereChallongeTournamentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereEnableLiveEditing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereGrandFinalsModifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereMatchAutoapi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereMatchAutostart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereRandomTeams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereTeamSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournament withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class EventTournament extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property int $challonge_match_id
 * @property int $game_server_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $event_tournament_id
 * @property-read \App\EventTournament $eventTournament
 * @property-read \App\GameServer $gameServer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereChallongeMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereEventTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereGameServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentMatchServer withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class EventTournamentMatchServer extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int|null $event_participant_id
 * @property string|null $challonge_participant_id
 * @property int|null $event_tournament_team_id
 * @property int $event_tournament_id
 * @property int|null $final_rank
 * @property string|null $final_history
 * @property string|null $final_ratio
 * @property int|null $final_score
 * @property int $credit_applied
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $pug
 * @property-read \App\EventParticipant|null $eventParticipant
 * @property-read \App\EventTournament $eventTournament
 * @property-read \App\EventTournamentTeam|null $tournamentTeam
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereChallongeParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereCreditApplied($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereEventParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereEventTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereEventTournamentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereFinalHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereFinalRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereFinalRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereFinalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant wherePug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentParticipant whereUpdatedAt($value)
 */
	class EventTournamentParticipant extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_tournament_id
 * @property string|null $challonge_participant_id
 * @property string $name
 * @property string $event_tournament_team_id
 * @property int|null $final_rank
 * @property string|null $final_history
 * @property string|null $final_ratio
 * @property int|null $final_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\EventTournament $eventTournament
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournamentParticipant> $tournamentParticipants
 * @property-read int|null $tournament_participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereChallongeParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereEventTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereEventTournamentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereFinalHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereFinalRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereFinalRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereFinalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTournamentTeam whereUpdatedAt($value)
 */
	class EventTournamentTeam extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $display_name
 * @property string $slug
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $address_street
 * @property string|null $address_city
 * @property string|null $address_postcode
 * @property string|null $address_country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventVenueImage> $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddressCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddressCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddressPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereAddressStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenue withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class EventVenue extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $event_venue_id
 * @property string $path
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\EventVenue|null $venue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage whereEventVenueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventVenueImage whereUpdatedAt($value)
 */
	class EventVenueImage extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $album_cover_id
 * @property int $event_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GalleryAlbumImage> $images
 * @property-read int|null $images_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereAlbumCoverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbum withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class GalleryAlbum extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $display_name
 * @property string $nice_name
 * @property int $filetype
 * @property string $path
 * @property string $url
 * @property string|null $desc
 * @property int|null $gallery_album_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\GalleryAlbum|null $album
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereFiletype($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereGalleryAlbumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereNiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GalleryAlbumImage whereUrl($value)
 */
	class GalleryAlbumImage extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $version
 * @property int $public
 * @property string|null $image_header_path
 * @property string|null $image_thumbnail_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $gamecommandhandler
 * @property string $connect_game_url
 * @property string $connect_game_command
 * @property string $connect_stream_url
 * @property int $min_team_count
 * @property int $max_team_count
 * @property int|null $matchStartgameServerCommand
 * @property int $gamematchapihandler
 * @property int $matchmaking_enabled
 * @property int $matchmaking_autostart
 * @property int $matchmaking_autoapi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventTournament> $eventTournaments
 * @property-read int|null $event_tournaments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GameServerCommandParameter> $gameServerCommandParameters
 * @property-read int|null $game_server_command_parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GameServerCommand> $gameServerCommands
 * @property-read int|null $game_server_commands_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GameServerCommand> $gameServerMatchCommands
 * @property-read int|null $game_server_match_commands_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\GameServer> $gameServers
 * @property-read int|null $game_servers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMaking> $matchMakings
 * @property-read int|null $match_makings_count
 * @property-read \App\GameServerCommand|null $matchStartGameServerCommand
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereConnectGameCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereConnectGameUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereConnectStreamUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereGamecommandhandler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereGamematchapihandler($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereImageHeaderPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereImageThumbnailPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMatchStartgameServerCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMatchmakingAutoapi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMatchmakingAutostart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMatchmakingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMaxTeamCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMinTeamCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Game extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $ispublic
 * @property int $isenabled
 * @property string $type
 * @property string|null $address
 * @property int|null $game_port
 * @property string|null $game_password
 * @property int|null $rcon_port
 * @property string|null $rcon_password
 * @property string|null $rcon_address
 * @property string $gameserver_secret
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $stream_port
 * @property-read \App\EventTournamentMatchServer|null $eventTournamentMatchServer
 * @property-read \App\Game $game
 * @property-read \App\MatchMakingServer|null $matchMakingServer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereGamePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereGamePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereGameserverSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereIsenabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereIspublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereRconAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereRconPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereRconPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereStreamPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServer withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class GameServer extends \Eloquent implements \Illuminate\Contracts\Auth\Authenticatable, \Illuminate\Contracts\Auth\Access\Authorizable {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $command
 * @property string|null $verification
 * @property int $scope
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand whereVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommand withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class GameServerCommand extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $options
 * @property int $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameServerCommandParameter withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class GameServerCommandParameter extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $album_cover_id
 * @property int $event_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\HelpCategoryEntry> $entrys
 * @property-read int|null $entrys_count
 * @property-read \App\Event|null $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereAlbumCoverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategory withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class HelpCategory extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $display_name
 * @property string $nice_name
 * @property string|null $content
 * @property int|null $help_category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\HelpCategoryEntryAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\HelpCategory|null $helpCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereHelpCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereNiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntry whereUpdatedAt($value)
 */
	class HelpCategoryEntry extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $display_name
 * @property string $nice_name
 * @property string $path
 * @property string $url
 * @property string|null $desc
 * @property int|null $help_category_entry_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\HelpCategoryEntry|null $entry
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereHelpCategoryEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereNiceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpCategoryEntryAttachment whereUrl($value)
 */
	class HelpCategoryEntryAttachment extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int|null $team_size
 * @property int|null $team_count
 * @property string $status
 * @property int $owner_id
 * @property string $invite_tag
 * @property int $ispublic
 * @property int|null $game_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game|null $game
 * @property-read \App\MatchMakingServer|null $matchMakingServer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchReplay> $matchReplays
 * @property-read int|null $match_replays_count
 * @property-read \App\MatchMakingTeam|null $oldestTeam
 * @property-read \App\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMakingTeamPlayer> $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMakingTeam> $teams
 * @property-read int|null $teams_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereInviteTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereIspublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereTeamCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereTeamSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMaking whereUpdatedAt($value)
 */
	class MatchMaking extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $match_id
 * @property int $game_server_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\GameServer $gameServer
 * @property-read \App\MatchMaking $match
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer whereGameServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingServer whereUpdatedAt($value)
 */
	class MatchMakingServer extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int|null $match_id
 * @property string $name
 * @property int|null $team_owner_id
 * @property int $team_score
 * @property string $team_invite_tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\MatchMaking|null $match
 * @property-read \App\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMakingTeamPlayer> $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereTeamInviteTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereTeamOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereTeamScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeam whereUpdatedAt($value)
 */
	class MatchMakingTeam extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $matchmaking_team_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\MatchMakingTeam $team
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer whereMatchmakingTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchMakingTeamPlayer whereUserId($value)
 */
	class MatchMakingTeamPlayer extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int|null $matchmaking_id
 * @property int|null $challonge_match_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\MatchMaking|null $matchMakingMatch
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereChallongeMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereMatchmakingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MatchReplay whereUpdatedAt($value)
 */
	class MatchReplay extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $article
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\NewsComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\NewsTag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsArticle withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class NewsArticle extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $comment
 * @property int $reviewed
 * @property int $reviewed_by
 * @property int $approved
 * @property int $approved_by
 * @property int $news_feed_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\NewsArticle $newsArticle
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\NewsCommentReport> $reports
 * @property-read int|null $reports_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereNewsFeedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereReviewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsComment whereUserId($value)
 */
	class NewsComment extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $comment
 * @property int $news_feed_comment_id
 * @property int $user_id
 * @property int $reviewed
 * @property int $reviewed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\NewsComment $newsComment
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereNewsFeedCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereReviewed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsCommentReport whereUserId($value)
 */
	class NewsCommentReport extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $tag
 * @property string $slug
 * @property int $news_feed_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\NewsArticle $newsArticle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereNewsFeedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NewsTag withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class NewsTag extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $status
 * @property int $allow_options_multi
 * @property int $allow_options_user
 * @property int $user_id
 * @property int|null $event_id
 * @property string|null $start
 * @property string|null $end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Event|null $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\PollOption> $options
 * @property-read int|null $options_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAllowOptionsMulti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAllowOptionsUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class Poll extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $poll_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Poll $poll
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\PollOptionVote> $votes
 * @property-read int|null $votes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereUserId($value)
 */
	class PollOption extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $poll_option_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\PollOption $pollOption
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote wherePollOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOptionVote whereUserId($value)
 */
	class PollOptionVote extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $transaction_id
 * @property string $paypal_email
 * @property string $token
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\CreditLog|null $creditLog
 * @property-read \App\ShopOrder|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventParticipant> $participants
 * @property-read int|null $participants_count
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase wherePaypalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUserId($value)
 */
	class Purchase extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $setting
 * @property string|null $value
 * @property string|null $description
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSetting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $featured
 * @property string $status
 * @property string|null $description
 * @property float|null $price
 * @property int|null $price_credit
 * @property int $shop_item_category_id
 * @property int $stock
 * @property int $added_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\ShopItemCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\ShopItemImage> $images
 * @property-read int|null $images_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem wherePriceCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereShopItemCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItem withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class ShopItem extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $order
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\ShopItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemCategory withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class ShopItemCategory extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $shop_item_id
 * @property string $path
 * @property int $order
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\ShopItem|null $item
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereShopItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopItemImage whereUpdatedAt($value)
 */
	class ShopItemImage extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $purchase_id
 * @property string $status
 * @property int $deliver_to_event
 * @property string|null $shipping_first_name
 * @property string|null $shipping_last_name
 * @property string|null $shipping_address_1
 * @property string|null $shipping_address_2
 * @property string|null $shipping_country
 * @property string|null $shipping_postcode
 * @property string|null $shipping_state
 * @property string|null $shipping_tracking
 * @property string|null $shipping_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\ShopOrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Purchase $purchase
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereDeliverToEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereShippingTracking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrder whereUpdatedAt($value)
 */
	class ShopOrder extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property int $shop_item_id
 * @property int $shop_order_id
 * @property float|null $price
 * @property int|null $price_credit
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\ShopItem|null $item
 * @property-read \App\ShopOrder|null $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem wherePriceCredit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereShopItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereShopOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShopOrderItem whereUpdatedAt($value)
 */
	class ShopOrderItem extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $slider_name
 * @property string $path
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage whereSliderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SliderImage whereUpdatedAt($value)
 */
	class SliderImage extends \Eloquent {}
}

namespace App{
/**
 * 
 *
 * @property int $id
 * @property string $firstname
 * @property string $surname
 * @property string|null $username
 * @property string|null $username_nice
 * @property string|null $steamname
 * @property string|null $email
 * @property string $phonenumber
 * @property string|null $password
 * @property string|null $avatar
 * @property string|null $steamid
 * @property int $admin
 * @property int $credit_total
 * @property string|null $remember_token
 * @property string $last_login
 * @property int $banned
 * @property string|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\CreditLog> $creditLogs
 * @property-read int|null $credit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\EventParticipant> $eventParticipants
 * @property-read int|null $event_participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMakingTeamPlayer> $matchMakingTeamplayers
 * @property-read int|null $match_making_teamplayers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\MatchMakingTeam> $matchMakingTeams
 * @property-read int|null $match_making_teams_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreditTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhonenumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSteamid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSteamname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsernameNice($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

