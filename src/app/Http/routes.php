<?php

/**
 * MIGRATION NOTICE
 *
 * This file previously contained all application routes (~765 lines).
 * It has been split into three focused route files as part of refactor [3.4-REFACTOR-1]:
 *
 *   routes/web.php   — public frontend, auth, install, and user-facing routes
 *   routes/api.php   — REST API, gameserver API, user API (Sanctum-guarded)
 *   routes/admin.php — all admin panel routes
 *
 * RouteServiceProvider now loads those three files instead of this one.
 * This file is retained for reference only and is no longer loaded.
 */
