# Breakdesigns Product Builder Template Overrides

The Breakdesigns Product Builder does not deliver template-overrides to group the configurated products in the cart.<br>
They are all shown as independant products like the user had added them manually.

With these template-overrides the configurated products are grouped together.<br>
Additionally the quantity-change and remove-from-cart options are removed for the configurated products.<br>
In this way the user cannot mess with the configuration and can only remove the whole configurated product.<br>

### Currently the following overrides are done:

- Virtuemart 
  - Version 4.4
  - Default Layout
  - Bootstrap 5 Layout
  - Installation-Path:
    - templates/YOUR-TEMPLATE/com_virtuemart/cart/

- VP OnePageCheckout Plugin
  - Version 7.28
  - Normal Layout
  - Narrow Layout
  - Installation-Path:
    - templates/YOUR-TEMPLATE/plg_system_vponepagecheckout/

- Rupostel OnePageCheckout 
  - Version 2.0.452.100125
  - Theme yootheme_2col
  - Installation-Path:
    - components/com_onepage/themes/YOUR-YOOTHEME-2COL-THEME/overrides/

- VP Neoteric Template
  - Version 1.9
  - Installation-Path:
    - templates/YOUR-TEMPLATE/com_virtuemart/cart/

<br>

Each override has as base the original version and the changes can be compared and adopted to other templates/extensions/plugins which create the cart layouts.

---

<b>The Breakdesigns Product Builder System Plugin is required!</b>


[![Download Breakdesigns Product Builder System Plugin](https://img.shields.io/github/v/release/Dudebaker/Breakdesigns-Product-Builder-System-Plugin?logo=github&label=Download%20Breakdesigns%20Product%20Builder%20System%20Plugin&color=blueviolet&style=for-the-badge)](https://github.com/Dudebaker/Breakdesigns-Product-Builder-System-Plugin/releases/download/v1.0.0/plg_system_breakdesignsproductbuilder.zip)
