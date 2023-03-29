Sprint 2

1. College, (modify, view document forwarded, track route )
   -in the modify the college only can replace the file or cancel the forwarding
   -once cancel a field name "active"(true or false) in document and routing table
2. Campus Extension Login
    - campus extension view of the file forwarded by college
    - campus extension modify the routing of the file 

3. Edit profile for college/ campus extension

--------------------------
Additonal Featuers on the Outgoing and Incoming Documents

Move to Outgoing Documents: You can add a button or link to each row in the Incoming Documents table that will trigger moving the document to the Outgoing Documents list. You'll need to create a new route and controller method to handle this action, update the document status, and then refresh the page or use AJAX to update the list.

Add Notes: You can add a feature to allow users to add notes to each document. This can be useful for keeping track of any required actions, comments, or other information related to the document. You can create a new table in your database to store the notes, and display them in a modal or dedicated view.

Forward to another department: You can add a feature to forward the document to another department, with a dropdown list of available departments. This would require creating a new route and controller method, updating the forwarding department information in the database, and refreshing the page or using AJAX to update the list.

Search and Filter: Implement search functionality to allow users to search documents based on specific criteria like document ID, document type, forwarding department, etc. Additionally, you


Additionally, you can implement filters to let users narrow down the list of incoming documents based on certain criteria like date range, document type, or department. This can help users find specific documents more efficiently.

Mark as complete: You can add an option for users to mark incoming documents as complete or processed. This would involve adding a new status to your document statuses (e.g., 'Processed') and creating a new route and controller method to update the document status in the database.

Document categorization: Allow users to categorize or tag documents based on their content, purpose, or other criteria. This can help users manage, search, and filter documents more effectively. You would need to create a new table in your database to store the categories or tags and update the document model to have a relationship with the new category or tag model.

Export to CSV or PDF: Implement a feature that allows users to export the list of incoming documents to a CSV or PDF file, which can be useful for reporting and analysis purposes. There are packages available for Laravel that can help with generating CSV and PDF files.

Batch actions: Allow users to perform actions on multiple documents at once, such as moving multiple documents to Outgoing Documents or forwarding multiple documents to a specific department. This would require creating new routes and controller methods to handle batch actions, and implementing checkboxes or another method for selecting multiple documents in the user interface.