package org.university.electronic.suppliesmanagementsystem.Models;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;

import java.util.List;

public class SalesStatistics {

    //generates a detailed sales and inventory report
    public String generateReport(Inventory inventory) {
        List<Item> items = inventory.getItems();

        if (items.isEmpty()) {
            return "No items in inventory to generate statistics.";
        }

        StringBuilder reportBuilder = new StringBuilder();
        reportBuilder.append("------ Sales and Inventory Statistics ------\n");
        reportBuilder.append(String.format("%-20s %-10s %-10s %-15s\n", "Item Name", "Quantity", "Price", "Total Sales"));

        double totalInventorySales = 0;

        for (Item item : items) {
            double totalSales = item.getStockLevel() * item.getSellingPrice();
            reportBuilder.append(String.format(
                    "%-20s %-10d %-10.2f %-15.2f\n",
                    item.getName(),
                    item.getStockLevel(),
                    item.getSellingPrice(),
                    totalSales
            ));
            totalInventorySales += totalSales;
        }

        reportBuilder.append("\nTotal Sales for All Items: ").append(String.format("%.2f", totalInventorySales));
        return reportBuilder.toString();
    }

    //provides an observable list of items for GUI integration
    public ObservableList<ItemStatistics> getItemStatistics(Inventory inventory) {
        List<Item> items = inventory.getItems();
        ObservableList<ItemStatistics> statistics = FXCollections.observableArrayList();

        for (Item item : items) {
            double totalSales = item.getStockLevel() * item.getSellingPrice();
            statistics.add(new ItemStatistics(item.getName(), item.getStockLevel(), item.getSellingPrice(), totalSales));
        }

        return statistics;
    }

    //helper class to hold item statistics
    public static class ItemStatistics {
        private final String name;
        private final int stockLevel;
        private final double sellingPrice;
        private final double totalSales;

        public ItemStatistics(String name, int stockLevel, double sellingPrice, double totalSales) {
            this.name = name;
            this.stockLevel = stockLevel;
            this.sellingPrice = sellingPrice;
            this.totalSales = totalSales;
        }

        public String getName() {
            return name;
        }

        public int getStockLevel() {
            return stockLevel;
        }

        public double getSellingPrice() {
            return sellingPrice;
        }

        public double getTotalSales() {
            return totalSales;
        }
    }
}
