package org.university.electronic.suppliesmanagementsystem.Controllers;

import java.io.*;
import java.util.ArrayList;
import java.util.List;

import org.university.electronic.suppliesmanagementsystem.Models.Item;
import org.university.electronic.suppliesmanagementsystem.Models.User;

public class DataPersistence {


    private static final String USERS_FILE_PATH =
            System.getProperty("user.home") + File.separator + "users.txt";

    private static final String ITEMS_FILE_PATH =
            System.getProperty("user.home") + File.separator + "inventory.txt";


    public static void saveUsers(String ignored, List<User> users) {
        try (BufferedWriter writer =
                     new BufferedWriter(new FileWriter(USERS_FILE_PATH))) {

            for (User user : users) {
                writer.write(
                        user.getUsername() + "," +
                                user.getPassword() + "," +
                                user.getRole()
                );
                writer.newLine();
            }

        } catch (IOException e) {
            System.err.println("Error saving users: " + e.getMessage());
        }
    }

    public static List<User> loadUsers(String ignored) {
        List<User> users = new ArrayList<>();
        File file = new File(USERS_FILE_PATH);


        if (file.exists()) {
            try (BufferedReader reader =
                         new BufferedReader(new FileReader(file))) {

                String line;
                while ((line = reader.readLine()) != null) {
                    String[] data = line.split(",");
                    users.add(new User(
                            data[0].trim(),
                            data[1].trim(),
                            data[2].trim()
                    ));
                }

            } catch (IOException e) {
                System.err.println("Error loading users from disk: " + e.getMessage());
            }
        }

        else {
            InputStream is = DataPersistence.class.getResourceAsStream(
                    "/users.txt");

            if (is == null) {
                System.err.println("Users file not found in resources.");
                return users;
            }

            try (BufferedReader reader =
                         new BufferedReader(new InputStreamReader(is))) {

                String line;
                while ((line = reader.readLine()) != null) {
                    String[] data = line.split(",");
                    users.add(new User(
                            data[0].trim(),
                            data[1].trim(),
                            data[2].trim()
                    ));
                }

            } catch (Exception e) {
                System.err.println("Error loading users from resources: " + e.getMessage());
            }
        }

        return users;
    }



    public static void saveItems(String ignored, List<Item> items) {
        try (BufferedWriter writer =
                     new BufferedWriter(new FileWriter(ITEMS_FILE_PATH))) {

            for (Item item : items) {
                writer.write(
                        item.getName() + "," +
                                item.getCategory() + "," +
                                item.getPurchasePrice() + "," +
                                item.getSellingPrice() + "," +
                                item.getStockLevel()
                );
                writer.newLine();
            }

        } catch (IOException e) {
            System.err.println("Error saving items: " + e.getMessage());
        }
    }

    public static List<Item> loadItems(String ignored) {
        List<Item> items = new ArrayList<>();
        File file = new File(ITEMS_FILE_PATH);

        if (!file.exists()) {
            return items;
        }

        try (BufferedReader reader =
                     new BufferedReader(new FileReader(file))) {

            String line;
            while ((line = reader.readLine()) != null) {
                String[] data = line.split(",");
                if (data.length == 5) {
                    items.add(new Item(
                            data[0],
                            data[1],
                            Double.parseDouble(data[2]),
                            Double.parseDouble(data[3]),
                            Integer.parseInt(data[4])
                    ));
                }
            }

        } catch (IOException e) {
            System.err.println("Error loading items: " + e.getMessage());
        }

        return items;
    }
}