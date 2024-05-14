# Mentalworkz Email Preview Magento 2.x Module

Testing and previewing transactional emails with actual data can be tricky and time consuming. This module allows you to easily preview frontend transactional emails via the admin with real data, and send test emails to your email client(s).


## 1. Documentation

**The email preview section can be accessed in four ways**

1. Admin menu -> Marketing -> Communications -> Preview/Send Email Templates
2. Admin menu -> Marketing -> Communications -> Email Templates -> Actions Column : Preview/Send
3. Email Template edit page -> Preview/Send button

For options 1 and 2, you will be redirected to the dedicated admin Email Preview section. If option 2, the preview section will be pre-loaded with the relevant email template.
For option 3, the Email Preview section will be loaded as a popup (so you are not redirected away from the email template edit page).  

<br />

**Within the admin Email Preview section, you have the following options:**

- Select an email template, this includes all site email templates and any overridden templates.
- Choose a store to emulate for multi-store sites, so you can preview emails for your multi-stores, if applicable
- Choose an entity(order, invoice, creditmemo, shipment, customer) and an entity ID. This allows you to load data into the email template. 
- Add email address(es) to send a copy of the email to, which allows you to preview your emails in different email clients

<br />

**The actual email preview area includes:**

- The details of any email template currently loaded
- Is resizeable:
  - You can either select a device from the list of devices listed to see how your email will generally look for that devices height/width
  - Enter custom values into the height/width input fields
  - Change orientation (landscape/portrait) by clicking the orientation button
  - Use the right hand side resize handle to manually adjust the preview area size, allowing you to quickly and easily see how your email content reacts as the viewable area changes.



## 2. Screenshots

![alt text](https://github.com/mentalworkz//blob/[branch]/image.jpg?raw=true)



## 3. How to install

#### Method 1: Install ready-to-paste package

Copy the module files to your site root **app/code/Mentalworkz/EmailPreview** directory, then run the following commands:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```


#### Method 2: Install via composer (Recommend)

Run the following command in Magento 2 root folder

```
composer require mentalworkz/module-email-preview
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```