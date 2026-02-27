package org.university.electronic.suppliesmanagementsystem.Models;

import java.io.*;
import java.nio.file.*;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.*;
import java.util.stream.Collectors;

public class BillManager {

    private final List<Bill> bills;

    public BillManager() {
        this.bills = new ArrayList<>();
        loadBillsFromFiles();
        System.out.println("Bills loaded: " + bills.size());
    }



    public void addBill(Bill bill) {
        bills.add(bill);
    }

    public List<Bill> getBills() {
        return bills;
    }

    public int getNextBillId() {
        Path billsDir = Paths.get("bills");
        int maxId = 0;

        if (!Files.exists(billsDir)) {
            return 1;
        }

        try {
            for (Path path : Files.list(billsDir).toList()) {
                String name = path.getFileName().toString();
                if (name.startsWith("Bill") && name.endsWith(".txt")) {
                    String number = name
                            .replace("Bill", "")
                            .replace(".txt", "");
                    maxId = Math.max(maxId, Integer.parseInt(number));
                }
            }
        } catch (Exception e) {
            System.err.println("Error determining next bill ID");
        }

        return maxId + 1;
    }

    public List<Bill> getBillsWithinDateRange(LocalDate startDate, LocalDate endDate) {
        return bills.stream()
                .filter(bill -> {
                    LocalDate billDate = bill.getBillDate().toLocalDate();
                    return !billDate.isBefore(startDate)
                            && !billDate.isAfter(endDate);
                })
                .collect(Collectors.toList());
    }


    public void saveBillToFile(Bill bill) {
        try {
            Path billsDir = Paths.get("bills");
            Files.createDirectories(billsDir);

            String filename = "bills/Bill" + bill.getBillNumber() + ".txt";

            try (BufferedWriter writer = new BufferedWriter(new FileWriter(filename))) {
                writer.write(bill.toString());
            }

        } catch (IOException e) {
            System.err.println("Error saving bill: " + e.getMessage());
        }
    }

    /* ================= LOAD FROM FILES ================= */

    private void loadBillsFromFiles() {
        Path billsDir = Paths.get("bills");

        if (!Files.exists(billsDir)) {
            System.out.println("Bills folder not found.");
            return;
        }

        try {
            Files.list(billsDir)
                    .filter(path -> path.toString().endsWith(".txt"))
                    .forEach(this::parseBillFile);
        } catch (IOException e) {
            System.err.println("Error reading bills folder: " + e.getMessage());
        }
    }

    private void parseBillFile(Path path) {
        try {
            List<String> lines = Files.readAllLines(path);

            if (lines.size() < 4) return;

            int billNumber = Integer.parseInt(
                    lines.get(0).replace("Bill Number:", "").trim()
            );

            LocalDateTime billDate = LocalDateTime.parse(
                    lines.get(1).replace("Date:", "").trim()
            );

            Bill bill = new Bill(billNumber, billDate);

            for (int i = 3; i < lines.size(); i++) {
                String line = lines.get(i).trim();

                if (line.startsWith("Total Amount")) break;
                if (!line.contains("Qty:") || !line.contains("Price:")) continue;

                // Emri i produktit
                String name = line.substring(0, line.indexOf("(")).trim();

                // Quantity
                int qtyIndex = line.indexOf("Qty:");
                int commaIndex = line.indexOf(",", qtyIndex);
                int qty = Integer.parseInt(
                        line.substring(qtyIndex + 4, commaIndex).trim()
                );

                // Price
                int priceIndex = line.indexOf("Price:");
                double price = Double.parseDouble(
                        line.substring(priceIndex + 6)
                                .replace("$", "")
                                .replace(")", "")
                                .trim()
                );

                bill.getBillItems().add(
                        new BillItem(name, price, qty)
                );
            }

            bills.add(bill);

        } catch (Exception e) {
            System.err.println("Error parsing bill file: " + path.getFileName());
            e.printStackTrace();
        }
    }
}