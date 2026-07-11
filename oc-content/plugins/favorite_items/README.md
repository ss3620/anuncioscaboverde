# Favorite Items — Osclass Plugin

A modern, intuitive **Favorites** plugin for Osclass 8.x that lets your visitors save the listings they like and return to them any time.

## Features

- **One-click favoriting** on every item detail page and on search-result cards (mini heart on the top-right corner).
- **"My Favorites" page** for logged-in users at `/user/favorites` — with cover image, title, price, "saved on" date, direct view link, and quick-remove.
- **"Most favorited listings" home-page section** — Delta theme renders this natively when `fi_most_favorited_items` is available (see `delta-compat.php`). On other themes the section can be injected via footer / `<?php osc_run_hook('favorite_items_home_widget'); ?>`.
- **User-menu badge** with a live count of the user's saved items.
- **Admin dashboard** with global stats (total favorites, unique users, unique items), top-favorited listings and top users.
- **Configurable UI** from the admin panel:
  - Icon style: **Heart** or **Star**
  - Icon color (hex)
  - Icon size
  - Button labels (add / active)
  - Show / hide favorite count
  - Home-page widget: enable/disable, title, subtitle, item count (1–24), minimum favorites threshold
- **Safety hooks**: automatically cleans favorites when an item or a user is deleted.
- **Performance**: single indexed table, no schema changes to core tables, uses Osclass DAO.

## Requirements

- Osclass **8.x** (tested on 8.1)
- PHP **7.4+**
- MySQL **5.7+** / MariaDB **10.3+**

## Installation

1. Download the `favorite_items.zip` file from this repository.
2. Log in to your Osclass admin panel.
3. Go to **Plugins → Manage plugins**.
4. Click **Add new** and upload `favorite_items.zip`.
5. Once the upload completes, click **Install** next to *Favorite Items*.
6. Click **Enable** to activate the plugin.
7. (Optional) Click **Configure** to change the icon style, color, or labels.

### Manual installation

If you prefer to install manually:

1. Unzip the archive.
2. Copy the `favorite_items/` folder into `oc-content/plugins/` on your server.
3. In the admin panel go to **Plugins → Manage plugins** and click **Install → Enable** for *Favorite Items*.

## Usage

Once enabled, the plugin works automatically:

- A **favorite button** appears on every item detail page (`item.php`).
- A **mini heart / star** appears on each search-result card.
- Logged-in users get a **"My Favorites"** entry in their user menu.
- Anonymous users clicking the button are redirected to the login page.

### URLs

| Purpose | URL |
|---|---|
| My Favorites page | `/index.php?page=custom&route=favorite-items-user-favorites` (or the friendly URL `/user/favorites` if friendly URLs are enabled) |
| Toggle endpoint (AJAX) | `/index.php?page=custom&route=favorite-items-toggle` (POST `item_id`) |
| Admin settings | *Plugins → Favorite Items → Settings* |
| Admin statistics | *Plugins → Favorite Items → Statistics* |

### Home page "Most favorited listings" section

- Automatically injected on the home page when enabled in Admin → Favorite Items → Settings → *Home page widget*.
- For **Delta 8.x** it slots in right after the "Latest items" block; for other themes it falls back to the last `.container` in `main`, then above the footer.
- Prefer explicit placement? Drop this line anywhere inside your theme's `home.php`:

  ```php
  <?php osc_run_hook('favorite_items_home_widget'); ?>
  ```

  When rendered this way the widget is displayed in-place and the automatic DOM relocator is skipped.

## Database

The plugin creates **one** table:

```sql
CREATE TABLE oc_t_item_favorite (
    fk_i_item_id INT UNSIGNED NOT NULL,
    fk_i_user_id INT UNSIGNED NOT NULL,
    dt_added     DATETIME     NOT NULL,
    PRIMARY KEY (fk_i_item_id, fk_i_user_id),
    INDEX idx_user (fk_i_user_id),
    INDEX idx_item (fk_i_item_id)
);
```

Uninstalling the plugin drops this table and removes plugin preferences.

## Theme integration (optional)

The plugin hooks into the following standard Osclass hooks — **no theme edit is required** if your theme calls them (most public themes do):

| Hook | Where it renders |
|---|---|
| `item_detail` | Favorite button on the listing detail page |
| `search_item` | Mini favorite button on search-result cards |
| `user_menu` | "My Favorites" menu entry |
| `header` | CSS + JS assets |

If your custom theme does **not** trigger these hooks, add these one-liners:

```php
// In your theme's item.php (inside the listing block)
osc_run_hook('item_detail');

// In your theme's search item loop
osc_run_hook('search_item');

// In your user menu template
osc_run_hook('user_menu');
```

## Development

File layout:

```
favorite_items/
├── index.php              # Plugin bootstrap, hooks, routes, install/uninstall
├── ModelFavorites.php     # Data-access class (extends Osclass DAO)
├── ajax.php               # AJAX endpoint (toggle / add / remove)
├── admin/
│   ├── settings.php       # Plugin settings admin page
│   └── stats.php          # Statistics admin page
├── views/
│   ├── favorite-button.php # Reusable button partial
│   └── favorites-page.php  # "My Favorites" user page
├── assets/
│   ├── css/favorite.css
│   └── js/favorite.js
└── README.md
```

## License

MIT © Emergent Labs
