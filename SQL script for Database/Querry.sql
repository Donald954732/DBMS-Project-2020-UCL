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
    use PHP mail() FUNCTION for #4 and #5
    #4 place it in so that it initiate when any buyer place a bid
*/
---
---getting the list of auction ending
SELECT AuctionID
FROM Auction
WHERE EndingTime = CURRENT_TIMESTAMP;

---retrieving the email
SELECT UserName, Email
FROM users
WHERE UserName = (
    SELECT Username
    FROM Bids
    WHERE AuctionID = $AuctionID AND BidPrice = (
        SELECT MAX(BidPrice)
        FROM bids
        WHERE AuctionID = $AuctionID
        )
    )
OR UserName = (
    SELECT Username
    FROM auctions
    WHERE auctionID = $AuctionID
    )
;

---example 
SELECT UserName, Email
FROM users
WHERE UserName = (
    SELECT Username
    FROM Bids
    WHERE AuctionID = 5 AND BidPrice = (
        SELECT MAX(BidPrice)
        FROM bids
        WHERE AuctionID = 5
        )
    )
OR UserName = (
    SELECT Username
    FROM auctions
    WHERE auctionID = 5
    )
;


/*
    use PHP mail() FUNCTION for #4 and #5
    #5 place it in so that it initiate when any buyer place a bid
*/
SELECT u.Email
FROM bids b RIGHT JOIN users u ON b.UserName = u.Username
WHERE b.AuctionID = $current_AuctionID
GROUP BY u.Email
HAVING MAX(b.BidPrice) < $current_BidPrice

UNION

SELECT u.Email
From watchlist w JOIN users u ON w.UserName = u.Username
WHERE w.AuctionID = $current_AuctionID AND w.UserName NOT IN (
    SELECT UserName
    FROM bids
    WHERE AuctionID = $current_AuctionID
);
---the not in part is to exclude the people who watchlisted it and bid it to not to interfere with the out bid checking

---example

SELECT u.Email
FROM bids b RIGHT JOIN users u ON b.UserName = u.Username
WHERE b.AuctionID = 5
GROUP BY u.Email
HAVING MAX(b.BidPrice) < 6000

UNION

SELECT u.Email
From watchlist w JOIN users u ON w.UserName = u.Username
WHERE w.AuctionID = 5 AND w.UserName NOT IN (
    SELECT UserName
    FROM bids
    WHERE AuctionID = 5
);

/*
    Recommandation #6
*/

---code with variable
SELECT b.AuctionID, (COUNT(b.UserName) / Count(b.UserName) OVER ()) AS 'Recommandation chance',
a.ItemName, a.ItemDescription, a.StartingPrice, 
TIMEDIFF(a.EndingTime, CURRENT_TIMESTAMP()) AS 'Time Remaining', COUNT(b.BidID) AS 'No. Of Bids', 
MAX(b.BidPrice) AS 'Current Price'
FROM bids b JOIN auctions a on b.AuctionID = a.AuctionID
WHERE b.UserName IN (SELECT Username
                    FROM bids
                    WHERE auctionID IN (
                        SELECT AuctionID
                        FROM bids
                        WHERE UserName = $currentUser
                    ) AND NOT UserName = $currentUser)
GROUP BY b.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime
ORDER BY (COUNT(b.UserName) / Count(b.UserName) OVER ()) DESC;



---example 
SELECT b.AuctionID, (COUNT(b.UserName) / Count(b.UserName) OVER ()) AS 'Recommandation chance',
a.ItemName, a.ItemDescription, a.StartingPrice, 
TIMEDIFF(a.EndingTime, CURRENT_TIMESTAMP()) AS 'Time Remaining', COUNT(b.BidID) AS 'No. Of Bids', 
MAX(b.BidPrice) AS 'Current Price'
FROM bids b JOIN auctions a on b.AuctionID = a.AuctionID
WHERE b.UserName IN (SELECT Username
                    FROM bids
                    WHERE auctionID IN (
                        SELECT AuctionID
                        FROM bids
                        WHERE UserName = 'aloofClam7'
                    ) AND NOT UserName = 'aloofClam7')
GROUP BY b.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime
ORDER BY (COUNT(b.UserName) / Count(b.UserName) OVER ()) DESC;

