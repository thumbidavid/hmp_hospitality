// resources/js/Stores/auctionStore.js

import { defineStore } from 'pinia';

export const useAuctionStore = defineStore('auction', {
    /**
     * STATE: The single source of truth for all auction data.
     */
    state: () => ({
        // Auction Details
        rfpId: null,
        rfpTitle: null,
        isEnded: false,
        isLoading: true,
        error: null,

        // Bidding Data
        leadingBid: 0,
        leadingBidder: null, // Can be 'You' or an anonymized ID
        myLastBid: 0,
        bidHistory: [],

        // Timer Data
        countdown: {
            hours: '00',
            minutes: '00',
            seconds: '00',
        },
        _timerInterval: null, // Private interval reference
    }),

    /**
     * GETTERS: Computed properties derived from the state.
     */
    getters: {
        isWinning: (state) => state.leadingBidder === 'You' && !state.isEnded,
        hasBeenOutbid: (state) => state.leadingBidder !== 'You' && !state.isEnded,
        wonAuction: (state) => state.leadingBidder === 'You' && state.isEnded,
        lostAuction: (state) => state.leadingBidder !== 'You' && state.isEnded,
    },

    /**
     * ACTIONS: Methods that change the state.
     */
    actions: {
        /**
         * Initializes the store with auction data.
         * In a real app, this would fetch from an API.
         */
        async fetchAuctionData(auctionId) {
            this.isLoading = true;
            this.error = null;

            // TODO: Replace this with a real API call (e.g., axios.get(`/api/auctions/${auctionId}`))
            // Simulating API call with a delay
            await new Promise(resolve => setTimeout(resolve, 500));

            try {
                // Dummy data representing the API response
                const data = {
                    rfpId: '#8A4-T56-9B1',
                    rfpTitle: 'Transport for Annual Gala',
                    myLastBid: 4950,
                    leadingBid: 4800,
                    leadingBidder: 'Competitor #A87',
                    endTime: new Date().getTime() + 1 * 60 * 60 * 1000 + 29 * 60 * 1000,
                    history: [
                        { id: 1, bidder: 'Competitor #B34', amount: 4990, timestamp: new Date(Date.now() - 3 * 60000) },
                        { id: 2, bidder: 'You', amount: 4950, timestamp: new Date(Date.now() - 2 * 60000) },
                        { id: 3, bidder: 'Competitor #A87', amount: 4800, timestamp: new Date(Date.now() - 1 * 60000) },
                    ]
                };

                // Update state with fetched data
                this.rfpId = data.rfpId;
                this.rfpTitle = data.rfpTitle;
                this.myLastBid = data.myLastBid;
                this.leadingBid = data.leadingBid;
                this.leadingBidder = data.leadingBidder;
                this.bidHistory = data.history.sort((a, b) => b.timestamp - a.timestamp);

                // Start the countdown timer
                this.startCountdown(data.endTime);

            } catch (e) {
                this.error = "Failed to load auction data.";
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * Simulates submitting a new bid.
         * @param {number} bidAmount
         */
        async submitBid(bidAmount) {
            // TODO: Replace this with a real API call (e.g., axios.post(...))
            console.log(`Submitting bid of ${bidAmount}`);
            await new Promise(resolve => setTimeout(resolve, 750));

            // On successful API response, update the state
            this.myLastBid = bidAmount;
            this.leadingBid = bidAmount;
            this.leadingBidder = 'You';
            this.bidHistory.unshift({ id: Date.now(), bidder: 'You', amount: bidAmount, timestamp: new Date() });
        },

        /**
         * Handles a new bid event coming from a WebSocket.
         * @param {object} bidData { bidder: string, amount: number, timestamp: Date }
         */
        handleIncomingBid(bidData) {
            this.leadingBid = bidData.amount;
            this.leadingBidder = bidData.bidder;
            this.bidHistory.unshift({ id: Date.now(), ...bidData });
        },

        /**
         * Manages the countdown timer interval.
         * @param {number} endTimeTimestamp
         */
        startCountdown(endTimeTimestamp) {
            // Clear any existing timer
            if (this._timerInterval) clearInterval(this._timerInterval);

            this._timerInterval = setInterval(() => {
                const distance = endTimeTimestamp - new Date().getTime();

                if (distance < 0) {
                    clearInterval(this._timerInterval);
                    this.isEnded = true;
                    this.countdown = { hours: '00', minutes: '00', seconds: '00' };
                    return;
                }

                this.countdown.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.countdown.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.countdown.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
            }, 1000);
        },
    }
});
