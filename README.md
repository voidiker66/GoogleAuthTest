# GoogleAuthTest


To use this Google Auth project:

Navigate to https://console.cloud.google.com/apis/credentials and create an OAuth 2.0 Client ID.

In the .env file, add `GOOGLE_CLIENT_ID={YOUR_CLIENT_ID}.apps.googleusercontent.com`


# What This Is

A simple Symfony webpage that allows you to receive (and verify) the user account that is signed in via Google on your local server.

# What This Does

The Google Sign In button on the main page is the standard sign in button you'll see on most sites (https://developers.google.com/identity/gsi/web).

The user is automatically signed out of Google (only for this application) so the user must sign in with the Google account they want to use for this site.

Once the user has signed in, the identity token is sent to the server, where the server verifies the identity token.

Once the identity token is verified, the server has access to the Google Account details (Account ID, Email Address, Account Name).

