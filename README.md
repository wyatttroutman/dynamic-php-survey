# Dynamic PHP Survey
Framework to build a dynamic PHP web user study to administer Amazon MTurk surveys.

## Description
This project was developed to administer an Amazon Mechanical Turk User Study. All material relevant to that study has been removed. The remaining files represent a proof-of-concept framework for administering a dynamically generated PHP user study with support for custom PHP modules. Please note that this project is not meant to be used, and it will not be updated. It is only listed to show one possible way to build a dynamic PHP website with the limitation of not using an MVC framework. That being said, it could be fully functional with some minor updates and refinement.

## Requirements
   - Web Server with active LAMP Stack
   - Database Server with active MySQL installation

## Installation
1. Clone the github repository to an appropriate, offline folder.
2. In your MySQL database, run the SQL database generation files provided in /sql/stored procedures to build the website database.
   _**Due to privacy concerns, I have removed the SQL database generation files, as this survery platform is still in use by a public institution. I apologize for not being able to provide those files at this time.**_
3. Update code as needed to suit your needs and publish.
   Update the Introduction text, Consent text, Exit text, and configure database options for each question.

## Building A Dynamic Survey
To build a survey, you will need to load a lot of data in to the generated database. To do this, I used MySQL Workbench's CSV import tool. If there is any intention of using this framework for a long period of  time, it would be wise to develop a front-end administrative page to load data in to the database. However, that was out of the scope of the original project implementation.

Tables that require data import include:
   - TASK
   - QUESTION_TYPE
   - QUESTION
   - ANSWER
   - QUESTION_ANSWER
   - CONTROL_TYPE
   - CONTROL_QUESTION
   - TASK_PAGE_QUESTION
   - MODULE (if applicable)

Don't worry, the database is developed with very restrictive foreign key contraints to prevent invalid data from entering the database. 

## Implementing a custom PHP module
As previously stated, this framework allows you to create custom modules to run at any point in the survey. To do this, create your module and save it in the modules folder. The module must, after it is completed, redirect the user back to the survey page. Anything can occur before that. To have the survey launch the module, save the module name as the description for a question in the database. Then, set the question as a single page question in the TASK_PAGE_QUESTION table. The survey will handle it from there. 

Provided as an example is a module that asks multiple questions depending on the current Task ID and allows the user to upload a photo to the uploads folder.

## Liability
This repository is provided as is. I make no representations or warranties of any kind concerning the legality, safety, suitability, lack of viruses, inaccuracies, typographical errors, or other harmful components of this repository. There are inherent dangers in the use of any software, and you are solely responsible for determining whether this software is compatible with your equipment and other software installed on your equipment. You are also solely responsible for the protection of your equipment and backup of your data, and I will not be liable for any damages you may suffer in connection with using, modifying, or distributing this software. This software is provided for educational purposes only.
