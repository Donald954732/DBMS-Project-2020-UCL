/*
    #1 Create User
*/

INSERT INTO users VALUES ($UserName, $AuthPassWord, $UserGroup, $Email);

/*
    #2 Create Auction
*/

INSERT INTO auctions (UserName, ItemName, ItemDescription, Category, StartingPrice, ReservePrice, EndingTime) 
VALUES ($UserName, $ItemName, $ItemDescription, $Category, $StartingPrice, $ReservePrice, $EndingTime);

--Create Bids
INSERT INTO bids (UserName, AuctionID, BidPrice, BidTime, Outcome) 
VALUES ($UserName, $AuctionID, $BidPrice, $BidTime, 'Pending');

/*
    #3 Searching
*/

---searching with text and sorted soonest expire
SELECT a.ItemName, a.ItemDescription, a.StartingPrice, 
TIMEDIFF(a.EndingTime, CURRENT_TIMESTAMP()) AS 'Time Remaining', COUNT(b.BidID) AS 'No. Of Bids', 
MAX(b.BidPrice) AS 'Current Price', a.StartingPrice
FROM auctions a JOIN bids b ON a.AuctionID = b.AuctionID
---search by itemname and remove expired
WHERE a.ItemName LIKE '%i%' AND (a.EndingTime - CURRENT_TIMESTAMP) > 0
GROUP BY a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime
ORDER BY (a.EndingTime - CURRENT_TIMESTAMP) ASC;


--- swap it out with different category
SELECT a.ItemName, a.ItemDescription, a.StartingPrice, 
TIMEDIFF(a.EndingTime, CURRENT_TIMESTAMP()) AS 'Time Remaining', COUNT(b.BidID) AS 'No. Of Bids',
MAX(b.BidPrice) AS 'Current Price', a.StartingPrice
FROM auctions a JOIN bids b ON a.AuctionID = b.AuctionID
---searching by categories
WHERE a.ItemName LIKE '%i%' AND (a.EndingTime - CURRENT_TIMESTAMP) > 0 
AND a.Category = 'Antiques' 
GROUP BY a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime
ORDER BY (a.EndingTime - CURRENT_TIMESTAMP) ASC;

--- sorted BY price
---searching with text and sorted soonest expire
SELECT a.ItemName, a.ItemDescription, a.StartingPrice, 
TIMEDIFF(a.EndingTime, CURRENT_TIMESTAMP()) AS 'Time Remaining', COUNT(b.BidID) AS 'No. Of Bids', 
MAX(b.BidPrice) AS 'Current Price', a.StartingPrice
FROM auctions a JOIN bids b ON a.AuctionID = b.AuctionID
---search by itemname and remove expired
WHERE a.ItemName LIKE '%i%' AND (a.EndingTime - CURRENT_TIMESTAMP) > 0
GROUP BY a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime
ORDER BY MAX(b.BidPrice) ASC;
---just sqap it with ASC and DESC


/*
    use PHP mail() FUNCTION for #4
*/

