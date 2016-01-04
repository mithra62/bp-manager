# MojiTrac #

MojiTrac is a next generation Project Management System (PMS) from Eric Lamb and [mithra62](http://www.mithra62.com "mithra62"). MojiTrac aims to solve the problem of scattered todo, task lists, and project info that's spread among various other systems and their accounts.

MojiTrac is written using [Zend Framework 2](http://framework.zend.com/ "Zend Framework 2") and will initially be a SaaS app with a self hosted and subscription support model once the key system is worked out. 

## The Problem ##

Working in client services, as either an agency or contract/freelancer, means using a myriad of systems with multiple accounts and credentials to do the work the clients need done. For example, a client may require a project to use Basecamp for Project Management, Github for VCS, and Harvest for time tracking. While another client/project could require accounts with ActiveCollab and Beanstalk or Bitbucket or any number of other scattered systems. 

So this creates a rather large problem for teams working on a project. With the details spread across so many systems, management of the projects can get complicated and create unnecessary pain and waste. 

MojiTrac will solve this by acting as a central repository for all of these systems so the core team can use one system while their clients can use whatever they like. Items posted by the client to Basecamp/ActiveCollab/Asana/Whatever will show up in MojiTrac while items posted to MojiTrac will appear in Basecamp/ActiveCollab/Asana/Whatever. 

## Audience ##

The anticipated customer base for MojiTrac is any company or individual who works with multiple clients who use different systems to manage their projects. Specifically, web development agencies and contractors/freelancers will be our initial goto audience though we'll have to widen the net at some point. 

## Design Model ##

At the core, MojiTrac is a stand alone PMS with all the expected functionality a PMS should have:

1. Project Management
2. Task Management
3. Company Management
4. Time Tracker / Timer Mechanism
5. Calendar View
6. Administrative Reports
7. Data Import / Export 
8. User and User Role Administration
9. IP Locker
10. Global Settings
11. REST API

### Sub Systems ###

In addition to the above, there are also numerous built in sub systems that are available for Projects, Tasks, and Companies:

1. Bookmarks
2. Notes
3. File Management
4. Discussions

### Extensiblity Model ###

In progress right now is to build in the ability to extend and modify as much about MojiTrac as possible. This is to include adding in new functionality and override/customize the existing business logic as well as themeing and complete controller/action/view override. 

All integration points between platforms (mentioned above) will be done through add-ons and not be included in the core. This will allow for a clean core codebase that focuses specifically on Project Management as well as provide us the ability to sell the integration functionality as stand alone pieces to be included as needed. 

The planned integration modules will be (in no particular order):

1. Basecamp
2. Beanstalk
3. Github
4. Harvest
5. ActiveCollab
6. Freshbooks
7. Bitbucket
8. Asana
9. MojiTrac (likely free)

As mentioned above, each integration module will be sold and marketed separately and not included by default. This way, customers can customize MojiTrac and only pay for the functionality they need/want. 

## Pricing Model ##

Initially, MojiTrac will be a hosted system with monthly, recurring, revenue. 

Eventually, MojiTrac will be sold as a 1 time purchase with a year of free upgrades. Future upgrades will require the customer join a subscription service. There will be no free support outside of a support forum style repository, documentation, FAQ, and other user generated support mechanisms. 

The initial price point for MojiTrac is still TBD but will likely be comparable with the competition (at around $200 to $500).

There will also be additional items, in terms of the integration add-ons mentioned above, as well as other items that are TBD. 

Further, all support where a person is required direct access to investigate will be charged for and require payment. This will likely hover around the $100 an hour mark though a subscription model, ala EllisLab, is being considered. 

