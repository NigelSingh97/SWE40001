--
-- Database: `requisitionDatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `NotificationMessages`
--

CREATE TABLE `NotificationMessages` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `message` varchar(1024) DEFAULT NULL,
  `isread` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `NotificationMessages`
--

INSERT INTO `NotificationMessages` (`id`, `userID`, `message`, `isread`) VALUES
(1, 4, 'Form ID 5 got newly filed', 1),
(2, 1, 'Form ID 5 got newly filed', 0),
(3, 5, 'New Forms validated , 5', 0),
(4, 2, 'Form ID 5 got approved', 0),
(5, 2, 'Form ID 1 got rejected', 0);

-- --------------------------------------------------------

--
-- Table structure for table `RequisitionFormComments`
--

CREATE TABLE `RequisitionFormComments` (
  `id` int(11) NOT NULL,
  `formID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `RequisitionFormComments`
--

INSERT INTO `RequisitionFormComments` (`id`, `formID`, `userID`, `time`, `comment`) VALUES
(1, 5, 5, '2022-06-02 02:12:15', 'Will be available soon'),
(2, 1, 1, '2022-06-02 02:15:14', 'Not avilable');

-- --------------------------------------------------------

--
-- Table structure for table `RequisitionFormLines`
--

CREATE TABLE `RequisitionFormLines` (
  `id` int(11) NOT NULL,
  `formID` int(11) NOT NULL,
  `product` varchar(200) DEFAULT NULL,
  `item` varchar(255) NOT NULL,
  `quantity` varchar(63) NOT NULL,
  `unitPrice` varchar(63) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `supplierAddress` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `RequisitionFormLines`
--

INSERT INTO `RequisitionFormLines` (`id`, `formID`, `product`, `item`, `quantity`, `unitPrice`, `supplier`, `supplierAddress`, `remarks`) VALUES
(1, 5, 'TV', 'Samsung TV', '1', '80000            ', 'supplier1', NULL, 'LED 54 inches');

-- --------------------------------------------------------

--
-- Table structure for table `RequisitionForms`
--

CREATE TABLE `RequisitionForms` (
  `id` int(11) NOT NULL,
  `timeSubmitted` datetime NOT NULL DEFAULT current_timestamp(),
  `userID` int(11) NOT NULL,
  `approvedState` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approverUserID` int(11) DEFAULT NULL,
  `levelStatus` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `RequisitionForms`
--

INSERT INTO `RequisitionForms` (`id`, `timeSubmitted`, `userID`, `approvedState`, `approverUserID`, `levelStatus`) VALUES
(1, '2022-06-02 01:58:30', 2, 'rejected', 1, NULL),
(5, '2022-06-02 02:03:55', 2, 'approved', 5, 'validated');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`) VALUES
(1, 'supplier1'),
(2, 'supplier2'),
(3, 'supplier3'),
(4, 'supplier4'),
(5, 'supplier5');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `id` int(11) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `password` varchar(63) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`id`, `userName`, `password`, `admin`, `type`) VALUES
(1, 'admin', 'admin', 1, 'Admin'),
(2, 'elias', 'pass', 0, 'Regular User'),
(3, 'john', 'pass', 0, 'Executive'),
(4, 'kane', 'pass', 0, 'Validator'),
(5, 'mack', 'pass', 0, 'Approver');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `NotificationMessages`
--
ALTER TABLE `NotificationMessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `RequisitionFormComments`
--
ALTER TABLE `RequisitionFormComments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formID` (`formID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `RequisitionFormLines`
--
ALTER TABLE `RequisitionFormLines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `formID` (`formID`);

--
-- Indexes for table `RequisitionForms`
--
ALTER TABLE `RequisitionForms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID` (`userID`),
  ADD KEY `approverUserID` (`approverUserID`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `NotificationMessages`
--
ALTER TABLE `NotificationMessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `RequisitionFormComments`
--
ALTER TABLE `RequisitionFormComments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `RequisitionFormLines`
--
ALTER TABLE `RequisitionFormLines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `RequisitionForms`
--
ALTER TABLE `RequisitionForms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `RequisitionFormComments`
--
ALTER TABLE `RequisitionFormComments`
  ADD CONSTRAINT `RequisitionFormComments_ibfk_1` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_10` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_11` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_12` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_13` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_14` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_15` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_16` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_17` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_18` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_19` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_20` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_21` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_22` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_3` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_4` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_5` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_6` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_7` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_8` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionFormComments_ibfk_9` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`);

--
-- Constraints for table `RequisitionFormLines`
--
ALTER TABLE `RequisitionFormLines`
  ADD CONSTRAINT `RequisitionFormLines_ibfk_1` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_10` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_11` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_2` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_3` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_4` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_5` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_6` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_7` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_8` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`),
  ADD CONSTRAINT `RequisitionFormLines_ibfk_9` FOREIGN KEY (`formID`) REFERENCES `RequisitionForms` (`id`);

--
-- Constraints for table `RequisitionForms`
--
ALTER TABLE `RequisitionForms`
  ADD CONSTRAINT `RequisitionForms_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_10` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_11` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_12` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_13` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_14` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_15` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_16` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_17` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_18` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_19` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_2` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_20` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_21` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_22` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_3` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_4` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_5` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_6` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_7` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_8` FOREIGN KEY (`approverUserID`) REFERENCES `Users` (`id`),
  ADD CONSTRAINT `RequisitionForms_ibfk_9` FOREIGN KEY (`userID`) REFERENCES `Users` (`id`);

