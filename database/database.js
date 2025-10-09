let database = {
    users: ["admin", "user", "guest"],
    posts: ["post1", "post2", "post3"],
    comments: ["comment1", "comment2", "comment3"],
    likes: ["like1", "like2", "like3"],
    followers: ["follower1", "follower2", "follower3"],
    following: ["following1", "following2", "following3"],
    messages: ["message1", "message2", "message3"],
    notifications: ["notification1", "notification2", "notification3"],
    settings: ["setting1", "setting2", "setting3"]
}

let local_database = {
    users: ["admin", "user", "guest"],
    posts: ["post1", "post2", "post3"],
    comments: ["comment1", "comment2", "comment3"],
    likes: ["like1", "like2", "like3"],
    followers: ["follower1", "follower2", "follower3"],
    following: ["following1", "following2", "following3"],
    messages: ["message1", "message2", "message3"],
    notifications: ["notification1", "notification2", "notification3"],
    settings: ["setting1", "setting2", "setting3"]
}

let database_manager = {
    database: database,
    local_database: local_database,
    database_manager: database_manager
}

let database_manager_local = {
    database: database,
    local_database: local_database,
    database_manager: database_manager
}

let database_manager_local_2 = {
    database: database,
    local_database: local_database,
    database_manager: database_manager
}

module.exports = { database, local_database, database_manager, database_manager_local };
