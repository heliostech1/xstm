

db.account.insert( [
  {"accountId": "sysadmin", "description": "System Administrator",  "systemAdminPassword":"4ac014a0cbc800fe42545143857f00715faf3ed9", "active":"Y" }
])

db.user_group.insert( [
  {"userGroupId": "admin", "name":"Administrator","orderInList":2, "color":"4BB336" },
  {"userGroupId": "manager", "name":"Manager User","orderInList":3, "color":"FF199C" },
  {"userGroupId": "supervisor", "name":"Supervisor","orderInList":1, "color":"5837FA" },
  {"userGroupId": "viewer", "name":"General User","orderInList":4, "color":"F50A39" },
])

