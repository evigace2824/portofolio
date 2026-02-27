// File: Models/Manager.java
package org.university.electronic.suppliesmanagementsystem.Models;

import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

public class Manager extends User {
    private Inventory inventory; //the inventory managed by the manager
    private List<Cashier> cashiers; //list of cashiers under the manager
    private List<Supplier> suppliers; //list of suppliers providing products

    public Manager(String username, String password, Inventory inventory, List<Cashier> cashiers, List<Supplier> suppliers) {
        super(username, password, "Manager");
        this.inventory = inventory;
        this.cashiers = cashiers != null ? cashiers : new ArrayList<>();
        this.suppliers = suppliers != null ? suppliers : new ArrayList<>();
    }

    public Inventory getInventory() {
        return inventory;
    }

    public List<Supplier> getSuppliers() {
        return suppliers;
    }

    //adds a new item category to the inventory
    public void addNewItemCategory(String name, String category, int stock, double purchasePrice, double sellingPrice) {
        Item newItem = new Item(name, category, purchasePrice, sellingPrice, stock);
        inventory.addItem(newItem);
        System.out.println("New item category added: " + name);
    }

    //restocks an existing item in the inventory
    public void restockItem(String name, int quantity) {
        Item item = inventory.findItemByName(name);
        if (item != null) {
            item.setStockLevel(item.getStockLevel() + quantity);
            inventory.saveInventory();
            System.out.println("Item restocked: " + name + " (" + quantity + " added)");
        } else {
            System.out.println("Item not found: " + name);
        }
    }

    //modifies an existing items details
    public void modifyItem(String name, int stock, double sellingPrice) {
        Item item = inventory.findItemByName(name);
        if (item != null) {
            item.setStockLevel(stock);
            item.setSellingPrice(sellingPrice);
            inventory.saveInventory();
            System.out.println("Item modified: " + name);
        } else {
            System.out.println("Item not found: " + name);
        }
    }

    //notifies if any item in the inventory is below the threshold
    public void checkLowStock(int threshold) {
        List<Item> lowStockItems = inventory.getItems().stream()
                .filter(item -> item.getStockLevel() < threshold)
                .toList();
        if (lowStockItems.isEmpty()) {
            System.out.println("No items below stock threshold.");
        } else {
            System.out.println("Low stock items:");
            for (Item item : lowStockItems) {
                System.out.println("- " + item.getName() + ": " + item.getStockLevel() + " left");
            }
        }
    }

    //monitor cashier performance
    public void monitorCashierPerformance() {
        for (Cashier cashier : cashiers) {
            double totalSales = cashier.getTotalSalesToday();
            int totalBills = cashier.getBills().size();
            int totalItemsSold = cashier.getBills().stream()
                    .flatMap(bill -> bill.getBillItems().stream())
                    .mapToInt(BillItem::getQuantity)
                    .sum();
            System.out.println("Cashier: " + cashier.getUsername() +
                    ", Total Sales Today: $" + totalSales +
                    ", Total Bills: " + totalBills +
                    ", Total Items Sold: " + totalItemsSold);
        }
    }

    //generates statistics based on time periods
    public void generateStatistics(LocalDateTime startDate, LocalDateTime endDate) {
        System.out.println("Sales Statistics from " + startDate + " to " + endDate + ":");
        for (Cashier cashier : cashiers) {
            double totalRevenue = cashier.getBills().stream()
                    .filter(bill -> !bill.getBillDate().isBefore(startDate) && !bill.getBillDate().isAfter(endDate))
                    .mapToDouble(Bill::getTotalAmount)
                    .sum();
            System.out.println("- Cashier: " + cashier.getUsername() + ", Total Revenue: $" + totalRevenue);
        }

        System.out.println("\nInventory Statistics:");
        for (Item item : inventory.getItems()) {
            System.out.println("- " + item.getName() + ": Stock = " + item.getStockLevel() +
                    ", Selling Price = " + item.getSellingPrice());
        }
    }

    //adds a supplier to the list
    public void addSupplier(Supplier supplier) {
        suppliers.add(supplier);
        System.out.println("Supplier added: " + supplier.getName());
    }

    //removes a supplier from the list
    public void removeSupplier(String supplierName) {
        suppliers.removeIf(supplier -> supplier.getName().equalsIgnoreCase(supplierName));
        System.out.println("Supplier removed: " + supplierName);
    }

    //gets a list of products offered by suppliers
    public Map<String, List<Supplier>> getSuppliersByProduct() {
        return suppliers.stream()
                .flatMap(supplier -> supplier.getProducts().stream().map(product -> Map.entry(product, supplier)))
                .collect(Collectors.groupingBy(Map.Entry::getKey,
                        Collectors.mapping(Map.Entry::getValue, Collectors.toList())));
    }
}
