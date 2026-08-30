# AGENTS.md — Candra Resort Project Context

## 1. Project Overview

**Project Name:** Candra Resort  
**Application Type:** Full-stack hotel reservation and hotel operational management system  
**Framework:** Laravel  
**Backend Language:** PHP  
**Frontend:** Laravel Blade  
**Authentication:** Manual authentication (do **not** use Laravel Breeze)  
**Online Payment Gateway:** Midtrans  
**Database:** Existing Laravel project database migrations have already been created  
**Primary Users / Roles:**
1. Guest
2. Receptionist
3. Owner

This project is not only a public hotel booking website. It is intended to become a compact hotel management system that covers the complete guest lifecycle:

**Public Website → Reservation → Payment → Check-In → Active Stay → Guest Services → Billing/Folio → Check-Out → Reporting**

The system must remain realistic for a hotel while still being maintainable as a Laravel training/final project.

---

# 2. IMPORTANT RULES FOR CODEX

Before changing or generating code:

1. **Inspect the current Laravel project first.**
2. **Inspect all existing migrations before creating models, relationships, controllers, services, seeders, or forms.**
3. Existing migrations are the **source of truth for the database schema**.
4. Do **not** recreate Laravel's default `users`, `password_reset_tokens`, or `sessions` tables.
5. The existing project already extends the default Laravel `users` table for the needs of Candra Resort.
6. Do not create duplicate migrations for tables that already exist.
7. If a required field appears missing, inspect the project first and explain the required migration change before implementing it.
8. Use Laravel Blade only. Do not introduce React, Vue, Inertia, Livewire, or Breeze unless explicitly requested later.
9. Authentication must be implemented manually.
10. Use the existing HTML templates in the project as the primary UI reference.
11. Do not replace the provided visual design with a generic Bootstrap/admin design.
12. Preserve the visual language, spacing, typography, colors, component style, sidebar style, cards, forms, tables, and navigation of the supplied templates while adapting their content to Candra Resort.
13. Prefer Laravel conventions, clear controllers/services, validation, policies/middleware, Eloquent relationships, transactions for critical flows, and reusable Blade components.
14. Avoid unnecessary complexity. Build the project module by module.
15. All destructive/important confirmation actions use **SweetAlert2**.
16. Normal success/error notification toasts use **Notyf**, displayed at the **top-right**.
17. Online payment uses **Midtrans**.
18. Offline payment methods are managed dynamically by Receptionist and may include Cash, Debit, QRIS, Bank Transfer, Card, etc.
19. Do not hard-code offline payment methods in application logic if they are represented in the existing database.
20. Financial transaction history should generally not be permanently deleted. Use status changes, cancellation/refund state, or soft deletion where appropriate.

---

# 3. Existing UI Templates

The project already contains HTML templates that must be used as design references.

## Guest / Public Landing Template

Look inside:

```text
resources/views/template-landing/
```

This directory contains `.html` templates for the public/guest-facing website.

Use these files as the visual reference for:

- Landing page
- Navbar
- Hero/banner
- Room listing
- Room detail
- Facilities
- Promotions
- Gallery
- About
- Contact
- Guest authentication pages where appropriate
- Guest dashboard where the design can reasonably inherit the same visual language

Do not blindly copy static HTML. Convert reusable portions into Laravel Blade layouts, components, and partials.

## Owner & Receptionist Dashboard Template

The project also contains a dashboard HTML template directory intended for **Owner** and **Receptionist**.

Inspect the actual directory name in `resources/views/` before implementation. It may be named similarly to:

```text
resources/views/template-dashboard/
```

Use those `.html` files as the design reference for:

- Dashboard layout
- Sidebar
- Top navigation
- Cards
- Forms
- Tables
- Badges
- Charts
- Modals
- Responsive behavior
- General colors and component styling

Owner and Receptionist can share the same general dashboard visual language, but their menus and accessible pages must be role-specific.

**Do not directly edit the reference template files unless necessary.** Prefer creating proper Blade views from them.

---

# 4. Recommended Blade View Structure

Use this structure as the preferred organization unless the existing project already has a compatible structure:

```text
resources/
└── views/
    ├── layouts/
    │   ├── guest.blade.php
    │   ├── receptionist.blade.php
    │   └── owner.blade.php
    │
    ├── components/
    │   ├── navbar.blade.php
    │   ├── footer.blade.php
    │   ├── sidebar-receptionist.blade.php
    │   ├── sidebar-owner.blade.php
    │   ├── breadcrumb.blade.php
    │   ├── status-badge.blade.php
    │   ├── empty-state.blade.php
    │   └── modal.blade.php
    │
    ├── partials/
    │   ├── notyf.blade.php
    │   ├── sweetalert.blade.php
    │   └── validation-errors.blade.php
    │
    ├── public/
    │   ├── home.blade.php
    │   ├── about.blade.php
    │   ├── gallery.blade.php
    │   ├── facilities.blade.php
    │   ├── contact.blade.php
    │   ├── rooms/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   └── promotions/
    │       └── index.blade.php
    │
    ├── auth/
    │   ├── login.blade.php
    │   ├── register.blade.php
    │   ├── forgot-password.blade.php
    │   └── reset-password.blade.php
    │
    ├── guest/
    │   ├── dashboard.blade.php
    │   ├── profile/
    │   │   ├── show.blade.php
    │   │   └── edit.blade.php
    │   ├── reservations/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   ├── show.blade.php
    │   │   └── payment.blade.php
    │   ├── payments/
    │   │   ├── show.blade.php
    │   │   └── success.blade.php
    │   └── history/
    │       └── index.blade.php
    │
    ├── room-service/
    │   ├── verify.blade.php
    │   ├── home.blade.php
    │   ├── food/
    │   │   ├── index.blade.php
    │   │   ├── show.blade.php
    │   │   └── cart.blade.php
    │   ├── services/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   ├── housekeeping/
    │   │   └── create.blade.php
    │   ├── assistance/
    │   │   └── create.blade.php
    │   ├── orders/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   └── bill/
    │       └── show.blade.php
    │
    ├── receptionist/
    │   ├── dashboard.blade.php
    │   ├── reservations/
    │   ├── checkin/
    │   ├── checkout/
    │   ├── guests/
    │   ├── room-types/
    │   ├── rooms/
    │   ├── facilities/
    │   ├── pricing/
    │   ├── promotions/
    │   ├── payments/
    │   ├── payment-methods/
    │   ├── food/
    │   │   ├── categories/
    │   │   ├── menus/
    │   │   └── orders/
    │   ├── hotel-services/
    │   ├── guest-requests/
    │   ├── folios/
    │   └── website/
    │
    ├── owner/
    │   ├── dashboard.blade.php
    │   ├── receptionists/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   ├── edit.blade.php
    │   │   └── show.blade.php
    │   └── reports/
    │       ├── reservations.blade.php
    │       ├── occupancy.blade.php
    │       ├── revenue.blade.php
    │       ├── payments.blade.php
    │       ├── services.blade.php
    │       └── monthly.blade.php
    │
    ├── template-landing/
    │   └── ... existing reference HTML files ...
    │
    ├── template-dashboard/
    │   └── ... existing reference HTML files ...
    │
    └── errors/
        ├── 403.blade.php
        ├── 404.blade.php
        └── 500.blade.php
```

If the existing dashboard template directory has another name, use the actual name and do not create an unnecessary duplicate.

---

# 5. Authentication and Roles

Authentication is custom/manual.

Roles:

```text
guest
receptionist
owner
```

## Guest

A Guest can self-register.

Guest registration should generally collect:

- Name
- Email
- Phone
- Password
- Password confirmation

Additional profile details can be completed later.

## Receptionist

Receptionist cannot self-register.

Receptionist account management belongs to the Owner.

The Owner can:

- Create Receptionist
- View Receptionists
- Update Receptionist
- Activate/deactivate Receptionist
- Reset Receptionist password
- Soft-delete/deactivate former staff where appropriate

If a Receptionist has transaction history, prefer **deactivation** over permanent deletion.

## Owner

Owner has a protected management account.

Owner is primarily read-only for hotel operational data except for Receptionist account management.

## Login Redirect

After login, redirect according to role:

```text
guest        → Guest area
receptionist → Receptionist dashboard
owner        → Owner dashboard
```

Use middleware to ensure role authorization.

Suggested middleware concepts:

```text
auth
role:guest
role:receptionist
role:owner
```

Never rely only on hiding menu items. Server-side authorization is required.

---

# 6. Core Business Concept

The most important lifecycle is:

```text
Public Website
    ↓
Search Availability
    ↓
Reservation
    ↓
Payment
    ↓
Confirmed Reservation
    ↓
Guest Arrives
    ↓
Receptionist Check-In
    ↓
Active Stay
    ↓
Room = Occupied
    ↓
Guest Uses Hotel Services
    ↓
Folio / Running Bill
    ↓
Receptionist Check-Out
    ↓
Final Payment
    ↓
Key Returned
    ↓
QR Access Revoked
    ↓
Room = Cleaning
    ↓
Room = Available
    ↓
Owner Reporting
```

**Reservation and Stay are different concepts.**

Reservation = booking before or until arrival.  
Stay = actual occupancy after check-in until check-out.

Do not merge these concepts unless the current schema explicitly does so.

---

# 7. Public Website / Guest Flow

Public visitors can browse the website without authentication.

Public features:

- Landing page
- Hotel information
- Room types
- Room details
- Room photos/gallery
- Facilities
- Prices
- Promotions
- Hotel gallery
- Contact information
- Availability search

Room detail should be complete and visually rich.

It should support data such as:

- Room type name
- Room description
- Base/current price
- Capacity
- Maximum adults
- Maximum children if supported by schema
- Bed type
- Bed count
- Room size
- Breakfast inclusion
- Facilities
- Main image
- Multiple gallery images
- Availability
- Promotion/rate information where relevant

Never reduce room details to only name + price.

---

# 8. Reservation Flow

Guest booking flow:

```text
Choose check-in date
    ↓
Choose check-out date
    ↓
Choose guest count
    ↓
Search available room types / rooms
    ↓
View room detail
    ↓
Choose room
    ↓
Apply promotion if available
    ↓
System calculates total
    ↓
Create reservation
    ↓
Choose payment
    ↓
Online → Midtrans
    ↓
Payment confirmed
    ↓
Reservation confirmed
```

Suggested reservation statuses may include the statuses already present in the database, for example:

```text
pending_payment
pending
confirmed
checked_in
checked_out
cancelled
no_show
```

Use the existing schema/status implementation as source of truth.

Availability logic must prevent double-booking for overlapping date ranges.

Price calculation should use existing rate/pricing tables and reservation-night snapshots if present in the migrations.

Historical reservations must preserve the original booking price even if room prices change later.

---

# 9. Walk-In Guest Flow

Receptionist can create a reservation for guests who arrive directly without booking online.

Walk-in flow:

```text
Guest arrives
    ↓
Receptionist checks room availability
    ↓
Receptionist creates walk-in reservation
    ↓
Collect guest information
    ↓
Select room
    ↓
Select payment method
    ↓
Confirm payment / deposit if required
    ↓
Continue to check-in
```

Walk-in reservations must be distinguishable from online reservations, using the existing schema.

Walk-in guests do not necessarily need to create a website account if the schema permits nullable guest user references.

The reservation must still preserve guest information such as:

- Name
- Phone
- Email if available

---

# 10. Payment System

There are two payment channels:

## A. Online Payment

Use **Midtrans**.

Midtrans can support methods such as:

- QRIS
- Virtual Account
- e-Wallet
- Cards
- Other Midtrans-supported methods

Never expose Midtrans Server Key in frontend code.

Keys/config belong in `.env`, such as conceptually:

```env
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false
```

Implement payment status synchronization using Midtrans callbacks/webhooks.

Payment success must be verified server-side.

Do not trust client redirect alone as proof of payment.

## B. Offline / Manual Payment

Receptionist manages payment methods via CRUD.

Examples:

- Cash
- Debit
- QRIS
- Bank Transfer
- Credit Card
- Other methods added later

These methods must come from the database rather than being permanently hard-coded.

Receptionist can:

- Create payment method
- View
- Edit
- Activate/deactivate
- Remove only if safe and unused, otherwise deactivate

Payment records should preserve historical method information and transaction history.

---

# 11. Check-In Flow

Check-in is processed by Receptionist.

Flow:

```text
Find reservation
    ↓
Validate reservation
    ↓
Check payment status / deposit
    ↓
Verify Guest identity
    ↓
Capture photo of KTP / other identity
    ↓
Record/check Guest phone number
    ↓
Assign physical room
    ↓
Record room key handover
    ↓
Create/activate Stay
    ↓
Reservation becomes checked-in
    ↓
Room becomes occupied
```

Guest identity card is physically returned after verification.

The system may store a photo of the identity document for hotel administration.

## Identity Photo Security

Do **not** store image binary directly in the database unless the existing schema explicitly requires it.

Prefer private Laravel storage and store only the path in the database.

Example concept:

```text
storage/app/private/guest-identities/...
```

Identity photos must not be publicly accessible by guessing a URL.

Only authorized Receptionist actions may retrieve them.

Owner does not need identity-photo access by default.

---

# 12. Room Status Management

Do not reduce room status to a simple boolean.

Use the statuses represented in the existing schema, conceptually:

```text
available
reserved
occupied
cleaning
maintenance
unavailable
```

Typical lifecycle:

```text
available
   ↓ reservation
reserved
   ↓ check-in
occupied
   ↓ check-out
cleaning
   ↓ room ready
available
```

Receptionist controls operational room status.

Maintenance/unavailable rooms must not be bookable.

---

# 13. Room QR Code System

Each physical hotel room has a **permanent QR code**.

Receptionist manages QR codes from the Room management UI.

When a room is created:

1. System automatically generates a unique QR token.
2. Receptionist does **not** manually type the QR token.
3. The QR belongs permanently to the room.
4. Receptionist can view/download/print the QR image.
5. Receptionist can regenerate the QR token if needed.

Suggested UI:

```text
Receptionist
└── Rooms
    └── Room Detail
        └── QR Code
            ├── View
            ├── Download
            ├── Print
            └── Regenerate
```

The QR image itself does not need to be stored in the database if it can be generated from the token.

## QR Security Rule — VERY IMPORTANT

The permanent room QR must **not** automatically grant room-service access.

Flow:

```text
Guest scans permanent room QR
    ↓
System identifies room from QR token
    ↓
Does this room currently have an ACTIVE STAY?
    ├── No → Access denied
    └── Yes
          ↓
       Ask Guest to enter phone number
          ↓
       Compare with phone number recorded at CHECK-IN
          ├── Different → Access denied
          └── Same → Access granted
```

**Do NOT use OTP unless explicitly requested later.**

Current requirement is **phone-number matching only**.

This protection exists because the QR code is permanent and someone could scan or photograph it when they are not the current guest.

The room-service access must only work while the corresponding stay is active.

After check-out, any active room-service session/access must be revoked.

---

# 14. Guest Room Service

After successful room QR verification, Guest can access a dedicated in-stay portal.

This is different from normal account login.

Room-service portal functions:

- Order food
- Order drinks
- Order hotel services
- Request massage
- Request spa
- Request laundry
- Request extra bed
- Request airport transfer if available
- Request housekeeping
- Request towels
- Request toiletries
- Request drinking water
- Contact/request assistance from Receptionist
- View orders
- View running bill/folio

A room-service session must be tied to:

- Guest/stay
- Room
- Active stay status
- Expiration/revocation state

Check-out must invalidate it.

---

# 15. Food & Beverage

Receptionist manages:

- Food categories
- Menu items
- Description
- Price
- Image
- Availability
- Preparation information if supported

Guest room-service order flow:

```text
Guest chooses menu
    ↓
Creates order
    ↓
requested
    ↓
Receptionist accepts
    ↓
accepted
    ↓
processing
    ↓
completed
```

Cancelled state should also be supported where appropriate.

Completed paid items should be reflected in the Guest folio.

---

# 16. Hotel Services

Receptionist manages services such as:

- Massage
- Spa
- Laundry
- Extra bed
- Airport transfer
- Other hotel services

Each service can have:

- Name
- Description
- Price
- Unit/type
- Availability/status
- Image if supported

Pricing units may include concepts such as:

```text
per_order
per_hour
per_item
per_kg
```

Use the current migrations as source of truth.

Guest can order services during an active stay.

Charges flow into the folio.

---

# 17. Housekeeping and Guest Requests

Guest can submit requests such as:

- Clean room
- Replace towel
- Replace bed linen
- Toiletries
- Drinking water
- General assistance
- Other request

Suggested request statuses:

```text
requested
accepted
processing
completed
cancelled
```

Priority can be used if present:

```text
low
normal
high
urgent
```

Receptionist should have an operational queue of pending guest requests.

---

# 18. Folio / Running Guest Bill

A folio represents the Guest's running hotel bill during a stay.

It may contain:

- Room charges
- Food
- Drinks
- Massage
- Spa
- Laundry
- Extra bed
- Other hotel services
- Manual charges
- Discounts
- Taxes/service charges if implemented

Example:

```text
Room Charge             Rp1,500,000
Food & Beverage           Rp250,000
Massage                    Rp300,000
Laundry                     Rp75,000
------------------------------------
Gross Total              Rp2,125,000

Previous Payment         Rp1,500,000
------------------------------------
Outstanding                Rp625,000
```

At check-out, Receptionist uses the folio to calculate final outstanding balance.

Do not calculate historical folios solely from current product prices. Preserve transaction-price snapshots.

---

# 19. Check-Out Flow

Receptionist handles check-out.

Flow:

```text
Open active Stay
    ↓
Review Guest Folio
    ↓
Verify all services/orders
    ↓
Calculate outstanding balance
    ↓
Receive final payment if needed
    ↓
Confirm room key returned
    ↓
Complete Stay
    ↓
Reservation = checked-out
    ↓
Revoke QR room-service access
    ↓
Room = cleaning
    ↓
Housekeeping/inspection completed
    ↓
Room = available
```

Check-out should use database transactions where multiple related records are updated.

---

# 20. Receptionist Responsibilities

Receptionist is the main hotel operational role.

Suggested sidebar/menu:

```text
Dashboard

Reservations
├── Reservation List
├── Create Walk-In Reservation
├── Check-In
└── Check-Out

Room Management
├── Room Types
├── Rooms
├── Facilities
├── Pricing
└── Promotions

Guest Services
├── Food Orders
├── Hotel Services
├── Housekeeping
└── Guest Requests

Finance
├── Payments
├── Payment Methods
└── Guest Folios

Food & Beverage
├── Categories
└── Menus

Website
├── Hotel Settings
├── Website Contents
└── Gallery
```

Receptionist can manage:

- Reservations
- Walk-in reservations
- Check-in
- Check-out
- Payments
- Payment methods
- Room types
- Physical rooms
- Room QR
- Facilities
- Room pricing
- Seasonal pricing
- Promotions
- Food categories
- Food menu
- Hotel services
- Guest requests
- Website content
- Gallery
- Hotel information

Receptionist cannot create other Receptionist accounts.

---

# 21. Receptionist Dashboard

Receptionist dashboard is primarily an **operational dashboard**, not a report dashboard.

Prioritize actionable information:

```text
Today's Arrivals
Today's Departures
Occupied Rooms
Available Rooms
Cleaning Rooms
Maintenance Rooms
Pending Check-In
Pending Payments
Active Guest Requests
Food Orders
```

Useful lists:

- Today's arrivals
- Today's departures
- Pending payments
- Active guest requests
- Recent food orders
- Room status overview

Avoid filling the page with unnecessary charts.

---

# 22. Owner Responsibilities

Owner primarily monitors business performance.

Owner does not normally perform:

- Check-in
- Check-out
- Room CRUD
- Reservation operations
- Food management
- Daily hotel operations

Owner functions:

- Dashboard
- Reservation reports
- Occupancy reports
- Revenue reports
- Payment reports
- Service reports
- Monthly reports
- Period comparison
- Export/print reports
- CRUD Receptionist accounts

---

# 23. Owner CRUD Receptionist

Owner can manage Receptionist accounts.

Functions:

```text
Create Receptionist
Read Receptionist
Update Receptionist
Activate / Deactivate Receptionist
Reset Password
Soft Delete if appropriate
```

When a Receptionist leaves the hotel, prefer:

```text
is_active = false
```

rather than deleting the account if the employee is referenced by historical transactions.

This preserves accountability such as:

- Who checked in a guest
- Who confirmed payment
- Who changed room rates
- Who processed check-out

---

# 24. Owner Dashboard & Reporting

Owner dashboard should contain business-oriented summary information.

Suggested metrics:

- Total reservations
- Occupancy rate
- Rooms occupied
- Rooms available
- Revenue today
- Revenue this month
- Reservation cancellations
- Average stay where practical

Charts/reports can include:

- Revenue by month
- Occupancy by month
- Revenue by category
- Room-type performance
- Food & beverage revenue
- Service revenue
- Payment method distribution
- Promotion usage
- Cancellation statistics

Date filters:

```text
Today
This Month
Last Month
3 Months
6 Months
This Year
Custom Range
```

Reports should be derived from actual transactional tables.

Do not create unnecessary static report tables unless required by current schema/design.

---

# 25. Room Management Requirements

Room management must be rich enough for a real hotel website.

## Room Type

Typical information can include, depending on existing migrations:

- Code
- Name
- Slug
- Description
- Capacity
- Max adults
- Max children
- Bed type
- Bed count
- Room size
- Base price
- Extra-bed price
- Breakfast included
- Active status

## Room Type Images

Support multiple images.

Needed concepts:

- Image path
- Alt text
- Caption
- Primary image
- Sort order

Public Room Detail should be able to display a gallery.

## Physical Room

Typical concepts:

- Room type
- Room number
- Floor
- Status
- QR token
- Notes
- Active status

Do not confuse Room Type ("Deluxe") with Physical Room ("Room 201").

---

# 26. Pricing

Support a default/base room price and flexible rates if available in the migrations.

Examples:

```text
Normal Rate
Weekend Rate
High Season
Holiday Rate
Special Rate
```

Reservation price must use the price applicable to each booked night.

Concept:

```text
Night 1 → Rp750,000
Night 2 → Rp850,000 weekend
Night 3 → Rp850,000 weekend
```

Promotion is applied after calculating the relevant room subtotal according to the chosen business rule.

Do not make historical reservations change when Receptionist later edits room rates.

---

# 27. Promotions

Receptionist can CRUD promotions.

Typical promotion data:

- Code
- Name
- Description
- Discount type
- Discount amount/percentage
- Start date
- End date
- Minimum transaction
- Maximum discount if applicable
- Usage quota if applicable
- Active status

System must validate promotion rules before applying them.

---

# 28. Website Content Management

Receptionist can manage public website content.

Possible content:

- Hotel name
- Hero/banner
- About section
- Contact
- Address
- Phone
- Email
- Social media
- Check-in/check-out information
- General facilities
- Gallery
- Promotions
- Policies
- Other dynamic content supported by the current schema

Do not require source-code changes for common website content updates if the database already supports dynamic content.

---

# 29. Notifications and Confirmations

## Notyf

Use Notyf for small notification/toast messages at **top-right**.

Examples:

- Data saved
- Reservation created
- Payment confirmed
- Profile updated
- Request submitted
- Error occurred

Centralize Notyf integration in a shared Blade partial/component.

## SweetAlert2

Use SweetAlert2 for modal confirmations and important actions.

Examples:

- Delete room?
- Cancel reservation?
- Confirm check-in?
- Confirm check-out?
- Regenerate room QR?
- Deactivate Receptionist?
- Delete promotion?

Do not use SweetAlert2 for every simple success message.

---

# 30. Audit Log

Important staff operations should be auditable if the existing migration supports audit logs.

Examples:

```text
Receptionist A confirmed payment
Receptionist A checked in Guest
Receptionist B changed room rate
Receptionist A checked out Guest
Owner created Receptionist account
Owner deactivated Receptionist account
```

Use audit data for accountability.

Do not expose sensitive values such as passwords, tokens, or Midtrans server keys in audit logs.

---

# 31. Security Requirements

Pay attention to the following:

1. Use Laravel validation / Form Request.
2. Use authorization middleware/policies.
3. Never trust role information from client input.
4. Hash passwords using Laravel's hashing facilities.
5. CSRF protection must remain enabled.
6. Midtrans callback must be verified server-side.
7. Identity photos must remain private.
8. QR tokens must be random and non-predictable.
9. Regenerating room QR must invalidate old token access.
10. Room-service access requires:
    - valid QR room token
    - active stay
    - matching check-in phone number
11. Check-out revokes room-service access.
12. Prevent double booking.
13. Use DB transactions on multi-table critical operations.
14. Validate uploaded images and file types.
15. Do not expose hidden/internal IDs unnecessarily in public URLs when tokens/slugs are more appropriate.
16. Prevent inactive Receptionist accounts from logging in.
17. Avoid permanently deleting financial records.

---

# 32. Laravel Architecture Guidance

Prefer conventional Laravel architecture.

Suggested areas:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   ├── Auth/
│   │   ├── Guest/
│   │   ├── RoomService/
│   │   ├── Receptionist/
│   │   └── Owner/
│   ├── Middleware/
│   └── Requests/
│
├── Models/
│
├── Services/
│   ├── ReservationService.php
│   ├── AvailabilityService.php
│   ├── PricingService.php
│   ├── CheckInService.php
│   ├── CheckOutService.php
│   ├── FolioService.php
│   ├── PaymentService.php
│   ├── MidtransService.php
│   └── RoomQrService.php
│
└── Support/
```

Only create service classes when business logic warrants them.

Do not put all business logic in controllers.

Controllers should focus on:

- Authorization
- Request handling
- Calling services/models
- Returning redirect/view/response

Use Form Requests for larger validation flows.

---

# 33. Suggested Controller Organization

Conceptually:

```text
Public/
├── HomeController
├── RoomController
├── PromotionController
└── ContactController

Auth/
├── LoginController
├── RegisterController
├── ForgotPasswordController
└── ResetPasswordController

Guest/
├── DashboardController
├── ProfileController
├── ReservationController
└── PaymentController

RoomService/
├── AccessController
├── FoodOrderController
├── ServiceOrderController
├── GuestRequestController
└── BillController

Receptionist/
├── DashboardController
├── ReservationController
├── CheckInController
├── CheckOutController
├── RoomTypeController
├── RoomController
├── FacilityController
├── PricingController
├── PromotionController
├── PaymentController
├── PaymentMethodController
├── FoodCategoryController
├── MenuItemController
├── FoodOrderController
├── HotelServiceController
├── ServiceOrderController
├── GuestRequestController
├── FolioController
└── WebsiteContentController

Owner/
├── DashboardController
├── ReceptionistController
└── ReportController
```

Adjust to the existing project and avoid creating classes that are not yet needed.

---

# 34. Route Organization

Prefer separate route groups by role and feature.

Concept:

```php
Route::get('/', ...);

Route::prefix('guest')
    ->middleware(['auth', 'role:guest'])
    ->name('guest.')
    ->group(...);

Route::prefix('receptionist')
    ->middleware(['auth', 'role:receptionist'])
    ->name('receptionist.')
    ->group(...);

Route::prefix('owner')
    ->middleware(['auth', 'role:owner'])
    ->name('owner.')
    ->group(...);
```

Room-service QR routes should use their own flow and middleware/session logic rather than normal Guest authentication only.

Use consistent named routes.

---

# 35. Code Style

Follow Laravel/PHP conventions.

- PSR-compatible formatting
- Descriptive names
- Thin controllers
- Reusable Blade partials/components
- Eloquent relationships
- Avoid duplicated queries
- Avoid N+1 queries; use eager loading where appropriate
- Use route model binding where appropriate
- Use transactions for check-in/check-out/payment critical flows
- Use enums/constants/value objects only when they simplify rather than overcomplicate
- Do not hard-code IDs
- Do not hard-code production URLs
- Use config/env for external services
- Prefer soft deletes where records have historical significance

---

# 36. Development Sequence

Implement in this order unless the user asks otherwise.

## Phase 1 — Understand Existing Project

1. Inspect migrations.
2. Inspect route files.
3. Inspect current models/controllers.
4. Inspect `template-landing`.
5. Inspect dashboard template directory.
6. Inspect assets and dependencies.
7. Verify Laravel/PHP version.

## Phase 2 — Foundation

1. Models + Eloquent relationships
2. Manual authentication
3. Role middleware
4. Base Blade layouts
5. Notyf integration
6. SweetAlert2 integration

## Phase 3 — Public Website

1. Landing page
2. Room listing
3. Room detail
4. Availability search
5. Facilities
6. Gallery
7. Promotions
8. Contact

Use `template-landing` design.

## Phase 4 — Receptionist Master Data

1. Room types
2. Room images
3. Rooms
4. QR generation/view/print
5. Facilities
6. Pricing
7. Promotions
8. Payment methods
9. Food categories/menu
10. Hotel services
11. Website content

## Phase 5 — Reservation

1. Guest booking
2. Walk-in booking
3. Availability engine
4. Price calculation
5. Promotion calculation

## Phase 6 — Payment

1. Manual/offline payment
2. Midtrans integration
3. Callback/webhook
4. Payment status updates

## Phase 7 — Check-In

1. Reservation validation
2. Guest identity photo
3. Phone verification/recording
4. Physical room assignment
5. Key handover
6. Active stay
7. Room occupied

## Phase 8 — QR Room Service

1. Permanent room QR
2. Active-stay validation
3. Phone-number matching
4. Room-service session
5. Food orders
6. Service orders
7. Housekeeping
8. Assistance
9. Bill view

## Phase 9 — Check-Out / Folio

1. Folio
2. Outstanding balance
3. Final payment
4. Key return
5. Complete stay
6. Revoke QR access
7. Room cleaning
8. Return room to available

## Phase 10 — Owner

1. Dashboard
2. Receptionist CRUD
3. Reservation report
4. Occupancy report
5. Revenue report
6. Payment report
7. Service report
8. Monthly report
9. Export

## Phase 11 — Finalization

1. Authorization review
2. Validation review
3. Audit logging
4. Responsive UI
5. Error pages
6. Seeders/demo data
7. Testing critical flows
8. Clean unused code

---

# 37. Definition of Important Terms

## Guest
Hotel customer.

## Receptionist
Operational hotel staff responsible for bookings, rooms, payments, check-in/out, and services.

## Owner
Business owner who monitors reports and manages Receptionist accounts.

## Room Type
Commercial room category such as Deluxe, Standard, Family, Suite.

## Room
Actual physical room such as room 101 or 202.

## Reservation
Booking record.

## Stay
Actual guest occupancy created/activated at check-in.

## Active Stay
Stay that has checked in but has not checked out.

## Folio
Running bill for a stay.

## Room QR
Permanent QR assigned to a physical room.

## Room Service Access
Temporary authorization generated after QR + active stay + phone-number verification.

---

# 38. Non-Negotiable Current Requirements

These requirements are explicitly decided and should not be changed silently:

- Project name is **Candra Resort**.
- Laravel Blade frontend.
- Manual authentication.
- No Laravel Breeze.
- Roles: Guest, Receptionist, Owner.
- Public Guest landing design comes from existing `template-landing`.
- Owner and Receptionist design comes from existing dashboard HTML template(s).
- Existing migrations are already present and must be respected.
- Owner CRUDs Receptionist accounts.
- Receptionist handles hotel operational CRUD.
- Guest can book online.
- Receptionist can create walk-in bookings.
- Online payment uses Midtrans.
- Offline payment methods are CRUD-managed by Receptionist.
- Room details include rich metadata, pricing, facilities, description, capacity, and multiple images.
- Each physical room has a permanent QR.
- Receptionist can view/download/print/regenerate room QR.
- QR verification uses **phone number matching only**, based on the phone recorded at check-in.
- No OTP for QR verification unless later requested.
- QR only works when the room has an active stay.
- After check-out, room-service access is revoked.
- Guest can order F&B and hotel services from the room QR portal.
- Guest can request housekeeping/help from the QR portal.
- Charges contribute to Guest folio.
- Receptionist processes check-in and check-out.
- Receptionist captures guest identity photo during check-in.
- Physical identity is returned to Guest.
- Identity photo must be stored privately.
- Room status includes realistic operational states.
- Notyf = top-right toast notifications.
- SweetAlert2 = confirmations/modal actions.
- Owner sees reports and business dashboard.
- Owner is not intended to operate daily hotel workflows.
- Financial/payment history should be preserved.
- Audit important staff actions where supported.

---

# 39. Instructions When Requirements Change

The user may revise the workflow during development.

When that happens:

1. Treat the latest explicit instruction as authoritative.
2. Update implementation consistently across:
   - database assumptions
   - models
   - services
   - controllers
   - routes
   - Blade views
   - validation
   - documentation
3. Do not keep obsolete flows active unless backward compatibility is intentionally required.
4. Before a destructive schema change, explain the impact.
5. Never silently change core business rules.

---

# 40. Final Goal

The completed Candra Resort application should feel like one coherent system consisting of three connected applications:

```text
1. PUBLIC / GUEST WEBSITE
   Hotel marketing + reservation + Guest account

2. RECEPTIONIST OPERATIONAL SYSTEM
   Reservations + rooms + payments + check-in/out + guest services

3. OWNER MANAGEMENT SYSTEM
   Reports + business monitoring + Receptionist account management
```

The final project should demonstrate full-stack Laravel skills including:

- Blade templating
- Manual authentication
- Role authorization
- CRUD
- Eloquent relationships
- Validation
- File/image upload
- Dynamic room gallery
- Reservation logic
- Availability checks
- Pricing
- Promotions
- Midtrans payment integration
- Walk-in transactions
- QR generation
- QR access validation
- Session/access management
- Guest services
- Folio/billing
- Check-in/check-out
- Reporting
- Notifications
- Confirmation modals
- Secure file access
- Responsive UI
- Reusable components
- Clean project organization

When working on this repository, prioritize correctness of the hotel business flow, consistency with the existing migrations, and visual consistency with the provided HTML templates.
