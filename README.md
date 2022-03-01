# About

This is the customer portal for OfficeLife. It allows customers to purchase a license that will let them use OfficeLife in production. This license is a product key that users must add to their OfficeLife account in order to use the product.

But first, let's answer some questions.

## Why is this not part of the main OfficeLife codebase?

OfficeLife is open source and can be freely used by anyone who is crazy enough to setup, store and maintain a custom instance. For those who take this approach, it doesn't make sense to have any information about subscriptions, purchasing information,… in the same codebase as the main application.

Moreover, even if this portal doesn't store any credit card information, data in this portal are not stored in the main OfficeLife database, reducing the risk of data breaches.

## Why publish this code? People will be able to easily pirate this.

Yes. Of course, by publishing this code, it's easier to crack the key that we require to use an account on OfficeLife. But companies who want to screw will do it anyway. So, we trust that people will play the game and pay for the product so we can continue to improve it and make it a sustainable company.

# Anatomy of a key

Each account of OfficeLife needs a valid key to work. The key contains the following information:

* `validated until date`: the date the key remains valid until,
* the number of max allowed employees for a given key,
* the company name,
* the email address of the person who made the purchase.

The key is a JSON that is encrypted with a key that we, and only we, possess. Customers simply have to copy and paste the encrypted key to their accounts to validate it.
