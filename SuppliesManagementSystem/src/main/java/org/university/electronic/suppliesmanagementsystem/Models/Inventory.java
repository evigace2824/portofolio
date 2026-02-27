package org.university.electronic.suppliesmanagementsystem.Models;

import java.io.*;
import java.util.ArrayList;
import java.util.List;

public class Inventory {

    private List<Item> items;


    private static final String INVENTORY_FILE_PATH =
            "src/main/resources/inventory.txt";

    public Inventory() {
        this.items = new ArrayList<>();
        loadInventory();
    }

    public List<Item> getItems() {
        return items;
    }

    public void addItem(Item item) {
        items.add(item);
        saveInventory();
    }

    public Item findItemByName(String name) {
        return items.stream()
                .filter(i -> i.getName().equalsIgnoreCase(name))
                .findFirst()
                .orElse(null);
    }


    public void saveInventory() {
        try (BufferedWriter writer =
                     new BufferedWriter(new FileWriter(INVENTORY_FILE_PATH))) {

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
            System.err.println("Error saving inventory: " + e.getMessage());
        }
    }


    public void loadInventory() {
        items.clear();
        File file = new File(INVENTORY_FILE_PATH);


        if (file.exists()) {
            try (BufferedReader reader =
                         new BufferedReader(new FileReader(file))) {

                String line;
                while ((line = reader.readLine()) != null) {
                    parseLine(line);
                }

                System.out.println("Inventory loaded from disk.");

            } catch (IOException e) {
                System.err.println("Error loading inventory from disk: " + e.getMessage());
            }
        }

        else {
            try (
                    InputStream is = getClass().getResourceAsStream(
                            "/org/university/electronic/suppliesmanagementsystem/inventory.txt");
                    BufferedReader reader =
                            new BufferedReader(new InputStreamReader(is))
            ) {
                if (is == null) {
                    System.out.println("Inventory file not found. Starting with empty inventory.");
                    return;
                }

                String line;
                while ((line = reader.readLine()) != null) {
                    parseLine(line);
                }

                System.out.println("Inventory loaded from resources.");

            } catch (Exception e) {
                System.err.println("Error loading inventory from resources: " + e.getMessage());
            }
        }
    }

    private void parseLine(String line) {
        String[] p = line.split(",");
        if (p.length == 5) {
            items.add(new Item(
                    p[0].trim(),
                    p[1].trim(),
                    Double.parseDouble(p[2]),
                    Double.parseDouble(p[3]),
                    Integer.parseInt(p[4])
            ));
        }
    }

    public List<Item> getLowStockItems(int threshold) {
        List<Item> low = new ArrayList<>();
        for (Item i : items) {
            if (i.getStockLevel() <= threshold) {
                low.add(i);
            }
        }
        return low;
    }
}