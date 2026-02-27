package org.university.electronic.suppliesmanagementsystem.Models;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import org.university.electronic.suppliesmanagementsystem.Controllers.DataPersistence;

public class UserManager {
    private List<User> users;
    private static final String FILE_NAME =
            System.getProperty("user.home") + File.separator + "users.txt";

    public UserManager() {
        this.users = new ArrayList<>();
        loadUsers();
    }

    //adds a new user to the list
    public void addUser(User user) {
        users.add(user);
        saveUsers(); //save to file after adding the user
    }

    //finds a user by their username
    public User findUserByUsername(String username) {
        for (User user : users) {
            if (user.getUsername().equalsIgnoreCase(username)) {
                return user;
            }
        }
        return null;
    }

    //removes a user from the list
    public void removeUser(User user) {
        users.remove(user);
        saveUsers(); //save to file after removing the user
    }

    //gets all users in the list
    public List<User> getUsers() {
        return users;
    }

    //updates a users details
    public void updateUser(User oldUser, User newUser) {
        int index = users.indexOf(oldUser);
        if (index != -1) {
            users.set(index, newUser);
            saveUsers(); //saves to file after updating the user
        }
    }

    //saves the list of users to the binary file
    public void saveUsers() {
        DataPersistence.saveUsers(FILE_NAME, users);
    }

    //loads the list of users from the binary file
    public void loadUsers() {
        List<User> loadedUsers = DataPersistence.loadUsers(FILE_NAME);

        if (loadedUsers != null && !loadedUsers.isEmpty()) {
            users.clear();
            users.addAll(loadedUsers);
            System.out.println("Users loaded: " + users.size());
        } else {
            System.out.println("Users file empty or not loaded.");
        }
    }

}
