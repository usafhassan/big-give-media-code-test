# big-give-media-code-test
Acme Widget Co are the leading provider of made up widgets and they’ve contracted me to create a proof of concept for their new sales system

# How it works #
There are three code files to keep the seperation of concerns
config.php - contains basic configration required for a Software/SAAS
core.php - all the main logic lives in this file
index.php - use facing view, for basic data entry/initializations and outputs.

Just open index.php in your browser to see the output with a few test samples, given in the original assignment. That's it

### Technical explanation ###
Apache with atleast php version 7 installed, is required to run this assignment.
If you want to remove the existing samples and reuse the index file then first you need to create instance of basket class.
After that, you can add desired products to that basket and then simply checkout.
The system displays checkout output into the browser

#### Assumptions and feedback ####
- For the special offer of buy one get one free, I had a confusion for the discount to be applied on every pair as such softwares usually add a restrictiction for no. of items when such a discount is given (as a competitor may order in bulks)
- With current implement there was no need of passing the data from basket constructor to the main Acme class, but I had to follow the points mentioned in the assignment. I might have kept the offers inside Acme class as that is the main class acting as a datastore / replacement of database
- I had no clue about how robust and dynamic the code is expected so I just tried to keep the structure solid such as if this software can be extended later
